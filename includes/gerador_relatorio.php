<?php

declare(strict_types=1);

function formatarPrecoExport(?float $valor): string
{
    if ($valor === null) {
        return 'Preço sob consulta.';
    }

    return 'R$ ' . number_format($valor, 2, ',', '.');
}

function formatarNumeroExport(float|int $valor, int $decimais = 0): string
{
    return number_format((float) $valor, $decimais, ',', '.');
}

function xmlEscape(string $valor): string
{
    return htmlspecialchars($valor, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function colunaExcel(int $indice): string
{
    $indice++;
    $coluna = '';

    while ($indice > 0) {
        $resto = ($indice - 1) % 26;
        $coluna = chr(65 + $resto) . $coluna;
        $indice = intdiv($indice - 1, 26);
    }

    return $coluna;
}

function gerarExcel(array $dados): void
{
    if (class_exists('ZipArchive')) {
        gerarExcelXlsx($dados);
        return;
    }

    gerarExcelXml($dados);
}

function gerarExcelXml(array $dados): void
{
    $linhas = montarLinhasExcel($dados);
    $rowsXml = '';

    foreach ($linhas as $linha) {
        $cellsXml = '';
        foreach ($linha as $valor) {
            $cellsXml .= '<Cell><Data ss:Type="String">' . xmlEscape((string) $valor) . '</Data></Cell>';
        }
        $rowsXml .= '<Row>' . $cellsXml . '</Row>';
    }

    $conteudo = '<?xml version="1.0" encoding="UTF-8"?>'
        . '<?mso-application progid="Excel.Sheet"?>'
        . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" '
        . 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">'
        . '<Worksheet ss:Name="Dimensionamento"><Table>' . $rowsXml . '</Table></Worksheet>'
        . '</Workbook>';

    $nomeArquivo = 'dimensionamento-offgrid-' . date('Y-m-d') . '.xls';

    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
    header('Content-Length: ' . strlen($conteudo));
    header('Cache-Control: max-age=0');

    echo $conteudo;
    exit;
}

function montarLinhasExcel(array $dados): array
{
    $linhas = [];
    $linhas[] = ['Componentes do Sistema - Dimensionamento Solar'];
    $linhas[] = ['Gerado em', $dados['gerado_em'] ?? ''];
    $linhas[] = [];
    $linhas[] = ['Categoria', 'Componente', 'Descrição', 'Quantidade', 'Valor unitário', 'Valor total', 'SKU'];

    foreach ($dados['componentes'] as $item) {
        $linhas[] = [
            $item['categoria'] ?? '',
            $item['nome'] ?? '',
            $item['descricao'] ?? '',
            (string) ($item['quantidade'] ?? 0),
            formatarPrecoExport(isset($item['preco_unitario']) ? (float) $item['preco_unitario'] : null),
            isset($item['preco_total']) ? formatarPrecoExport((float) $item['preco_total']) : formatarPrecoExport(null),
            (string) ($item['sku'] ?? ''),
        ];
    }

    $linhas[] = [];
    $linhas[] = ['Valor total dos equipamentos', '', '', '', '', formatarPrecoExport((float) ($dados['valor_total'] ?? 0))];

    if (!empty($dados['itens_sem_preco'])) {
        $linhas[] = [($dados['itens_sem_preco'] ?? 0) . ' item(ns) sem preço — não incluídos no total'];
    }

    $linhas[] = [];
    $linhas[] = ['Resumo de dimensionamento'];
    $linhas[] = ['Componente', 'Necessário', 'Gerada', 'Unidade'];

    foreach ($dados['resumo'] as $item) {
        $linhas[] = [
            $item['nome'] ?? '',
            formatarNumeroExport((float) ($item['necessario'] ?? 0), 2),
            formatarNumeroExport((float) ($item['gerado'] ?? 0), 2),
            $item['unidade'] ?? '',
        ];
    }

    return $linhas;
}

function gerarExcelXlsx(array $dados): void
{
    $linhas = montarLinhasExcel($dados);
    $sheetRows = '';

    foreach ($linhas as $rowIndex => $linha) {
        $numeroLinha = $rowIndex + 1;
        $cells = '';

        foreach ($linha as $colIndex => $valor) {
            $referencia = colunaExcel($colIndex) . $numeroLinha;
            $texto = xmlEscape((string) $valor);
            $cells .= '<c r="' . $referencia . '" t="inlineStr"><is><t>' . $texto . '</t></is></c>';
        }

        $sheetRows .= '<row r="' . $numeroLinha . '">' . $cells . '</row>';
    }

    $sheetXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<sheetData>' . $sheetRows . '</sheetData>'
        . '</worksheet>';

    $workbookXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
        . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets><sheet name="Dimensionamento" sheetId="1" r:id="rId1"/></sheets>'
        . '</workbook>';

    $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>';

    $workbookRelsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
        . '</Relationships>';

    $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
        . '</Types>';

    $arquivoTemp = tempnam(sys_get_temp_dir(), 'xlsx_');
    $zip = new ZipArchive();

    if ($zip->open($arquivoTemp, ZipArchive::OVERWRITE) !== true) {
        http_response_code(500);
        echo 'Não foi possível gerar o arquivo Excel.';
        exit;
    }

    $zip->addFromString('[Content_Types].xml', $contentTypesXml);
    $zip->addFromString('_rels/.rels', $relsXml);
    $zip->addFromString('xl/workbook.xml', $workbookXml);
    $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
    $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
    $zip->close();

    $nomeArquivo = 'dimensionamento-offgrid-' . date('Y-m-d') . '.xlsx';

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
    header('Content-Length: ' . filesize($arquivoTemp));
    header('Cache-Control: max-age=0');

    readfile($arquivoTemp);
    unlink($arquivoTemp);
    exit;
}

final class RelatorioPdf
{
    private array $paginas = [];
    private int $paginaAtual = -1;
    private float $cursorY = 40.0;
    private float $margem = 40.0;
    private float $larguraPagina = 595.0;
    private float $alturaPagina = 842.0;

    public function adicionarPagina(): void
    {
        $this->paginaAtual++;
        $this->paginas[$this->paginaAtual] = [];
        $this->cursorY = $this->alturaPagina - $this->margem;
    }

    public function adicionarTexto(string $texto, float $tamanho = 11, bool $negrito = false): void
    {
        $this->garantirEspaco($tamanho + 6);
        $fonte = $negrito ? '/F2' : '/F1';
        $this->paginas[$this->paginaAtual][] = sprintf(
            "BT /F%s %.2F Tf %.2F %.2F Td (%s) Tj ET",
            substr($fonte, 2),
            $tamanho,
            $this->margem,
            $this->cursorY,
            $this->escaparPdf($texto)
        );
        $this->cursorY -= $tamanho + 6;
    }

    public function adicionarEspaco(float $altura = 10): void
    {
        $this->cursorY -= $altura;
    }

    public function adicionarTabela(array $cabecalho, array $linhas, array $larguras): void
    {
        $alturaLinha = 16.0;
        $tamanhoFonte = 8.5;

        $this->garantirEspaco($alturaLinha + 4);
        $this->desenharLinhaTabela($cabecalho, $larguras, $alturaLinha, $tamanhoFonte, true);

        foreach ($linhas as $linha) {
            $this->garantirEspaco($alturaLinha + 2);
            $this->desenharLinhaTabela($linha, $larguras, $alturaLinha, $tamanhoFonte, false);
        }
    }

    public function enviar(string $nomeArquivo): void
    {
        if ($this->paginaAtual < 0) {
            $this->adicionarPagina();
        }

        $objetos = [];
        $objetos[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $kids = [];

        for ($i = 0; $i <= $this->paginaAtual; $i++) {
            $conteudoId = 3 + ($i * 2);
            $paginaId = 4 + ($i * 2);
            $conteudo = implode("\n", $this->paginas[$i]);
            $objetos[$conteudoId] = '<< /Length ' . strlen($conteudo) . " >>\nstream\n" . $conteudo . "\nendstream";
            $objetos[$paginaId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 '
                . $this->larguraPagina . ' ' . $this->alturaPagina
                . '] /Contents ' . $conteudoId . ' 0 R /Resources << /Font << /F1 5 0 R /F2 6 0 R >> >> >>';
            $kids[] = $paginaId . ' 0 R';
        }

        $objetos[2] = '<< /Type /Pages /Kids [' . implode(' ', $kids) . '] /Count ' . count($kids) . ' >>';
        $objetos[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objetos[6] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        ksort($objetos);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        $maxId = max(array_keys($objetos));

        for ($id = 1; $id <= $maxId; $id++) {
            if (!isset($objetos[$id])) {
                continue;
            }
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $objetos[$id] . "\nendobj\n";
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . ($maxId + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $maxId; $id++) {
            $offset = $offsets[$id] ?? 0;
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size " . ($maxId + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $nomeArquivo . '"');
        header('Content-Length: ' . strlen($pdf));
        header('Cache-Control: max-age=0');

        echo $pdf;
        exit;
    }

    private function garantirEspaco(float $necessario): void
    {
        if ($this->paginaAtual < 0) {
            $this->adicionarPagina();
        }

        if ($this->cursorY - $necessario < $this->margem) {
            $this->adicionarPagina();
        }
    }

    private function desenharLinhaTabela(array $celulas, array $larguras, float $alturaLinha, float $tamanhoFonte, bool $cabecalho): void
    {
        $x = $this->margem;
        $y = $this->cursorY;

        foreach ($celulas as $indice => $texto) {
            $largura = $larguras[$indice] ?? 80;
            $fonte = $cabecalho ? '2' : '1';
            $textoFormatado = $this->truncarTexto((string) $texto, (int) floor($largura / ($tamanhoFonte * 0.45)));
            $this->paginas[$this->paginaAtual][] = sprintf(
                "BT /F%s %.2F Tf %.2F %.2F Td (%s) Tj ET",
                $fonte,
                $tamanhoFonte,
                $x + 2,
                $y - 12,
                $this->escaparPdf($textoFormatado)
            );
            $x += $largura;
        }

        $larguraTotal = array_sum($larguras);
        $topo = $y;
        $base = $y - $alturaLinha;
        $this->paginas[$this->paginaAtual][] = sprintf(
            '%.2F %.2F m %.2F %.2F l %.2F %.2F l %.2F %.2F l %.2F %.2F l S',
            $this->margem,
            $topo,
            $this->margem + $larguraTotal,
            $topo,
            $this->margem + $larguraTotal,
            $base,
            $this->margem,
            $base,
            $this->margem,
            $topo
        );

        $this->cursorY -= $alturaLinha;
    }

    private function truncarTexto(string $texto, int $maximo): string
    {
        if ($maximo < 4) {
            return $texto;
        }

        if (mb_strlen($texto, 'UTF-8') <= $maximo) {
            return $texto;
        }

        return mb_substr($texto, 0, $maximo - 3, 'UTF-8') . '...';
    }

    private function escaparPdf(string $texto): string
    {
        $convertido = @iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
        if ($convertido === false) {
            $convertido = $texto;
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $convertido);
    }
}

function gerarPdf(array $dados): void
{
    $pdf = new RelatorioPdf();
    $pdf->adicionarPagina();
    $pdf->adicionarTexto('Componentes do Sistema', 16, true);
    $pdf->adicionarTexto('Lista de materiais dimensionados', 11);
    $pdf->adicionarTexto('Gerado em: ' . ($dados['gerado_em'] ?? ''), 10);
    $pdf->adicionarEspaco(8);

    $linhasComponentes = [];
    foreach ($dados['componentes'] as $item) {
        $linhasComponentes[] = [
            $item['categoria'] ?? '',
            $item['nome'] ?? '',
            $item['descricao'] ?? '',
            (string) ($item['quantidade'] ?? 0),
            formatarPrecoExport(isset($item['preco_unitario']) ? (float) $item['preco_unitario'] : null),
            (string) ($item['sku'] ?? ''),
        ];
    }

    $pdf->adicionarTexto('Lista de componentes', 12, true);
    $pdf->adicionarTabela(
        ['Categoria', 'Componente', 'Descrição', 'Qtd', 'Valor unit.', 'SKU'],
        $linhasComponentes,
        [70, 75, 150, 35, 75, 55]
    );

    $pdf->adicionarEspaco(10);
    $pdf->adicionarTexto(
        'Valor total dos equipamentos: ' . formatarPrecoExport((float) ($dados['valor_total'] ?? 0)),
        11,
        true
    );

    if (!empty($dados['itens_sem_preco'])) {
        $pdf->adicionarTexto(
            ($dados['itens_sem_preco'] ?? 0) . ' item(ns) sem preço — não incluídos no total',
            9
        );
    }

    $pdf->adicionarEspaco(12);
    $pdf->adicionarTexto('Resumo de dimensionamento', 12, true);

    $linhasResumo = [];
    foreach ($dados['resumo'] as $item) {
        $linhasResumo[] = [
            $item['nome'] ?? '',
            formatarNumeroExport((float) ($item['necessario'] ?? 0), 2),
            formatarNumeroExport((float) ($item['gerado'] ?? 0), 2),
            $item['unidade'] ?? '',
        ];
    }

    $pdf->adicionarTabela(
        ['Componente', 'Necessário', 'Gerada', 'Unidade'],
        $linhasResumo,
        [160, 90, 90, 70]
    );

    $pdf->enviar('dimensionamento-offgrid-' . date('Y-m-d') . '.pdf');
}
