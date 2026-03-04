<?php
declare(strict_types=1);

require_once 'models/Reporte.php';
require_once 'helpers/CsrfHelper.php';

/**
 * Controlador avanzado de reportes
 */
class ReporteAvanzadoController {
    private PDO $pdo;
    private Reporte $reporteModel;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
        $this->reporteModel = new Reporte($pdo);
    }

    /**
     * Dashboard de reportes
     */
    public function index(): array {
        return [
            'resumen' => $this->reporteModel->getResumenGeneral()
        ];
    }

    /**
     * Reporte de consumibles con filtros
     */
    public function consumibles(array $filtros): array {
        return [
            'movimientos' => $this->reporteModel->getMovimientosConsumibles($filtros),
            'estadisticas' => $this->reporteModel->getEstadisticasConsumibles(),
            'consumo_area' => $this->reporteModel->getConsumoPorArea(),
            'consumo_mes' => $this->reporteModel->getConsumoPorMes(),
            'bajo_stock' => $this->reporteModel->getProductosBajoStock(),
            'filtros' => $filtros
        ];
    }

    /**
     * Reporte de activos fijos con filtros
     */
    public function activos(array $filtros): array {
        return [
            'bienes' => $this->reporteModel->getBienes($filtros),
            'estadisticas_estado' => $this->reporteModel->getEstadisticasBienes(),
            'bienes_area' => $this->reporteModel->getBienesPorArea(),
            'movimientos' => $this->reporteModel->getMovimientosActivos($filtros),
            'actividades_usuario' => $this->reporteModel->getActividadesPorUsuario($filtros),
            'filtros' => $filtros
        ];
    }

    /**
     * Genera reporte PDF de consumibles
     */
    public function generarPdfConsumibles(array $filtros): void {
        $datos = $this->consumibles($filtros);
        
        // Generar HTML para PDF
        $html = $this->generarHtmlPdf('Reporte de Consumibles', $datos['movimientos'], $filtros);
        
        // Descargar como PDF (usando método alternativo)
        $this->descargarPdf($html, 'reporte_consumibles_' . date('Ymd_His'));
    }

    /**
     * Genera reporte PDF de activos
     */
    public function generarPdfActivos(array $filtros): void {
        $datos = $this->activos($filtros);
        
        $html = $this->generarHtmlPdf('Reporte de Activos Fijos', $datos['bienes'], $filtros);
        
        $this->descargarPdf($html, 'reporte_activos_' . date('Ymd_His'));
    }

    /**
     * Genera HTML para PDF
     */
    private function generarHtmlPdf(string $titulo, array $datos, array $filtros): string {
        $fecha = date('d/m/Y H:i:s');
        
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>' . $titulo . '</title>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .header h1 { margin: 0; color: #333; }
                .header .fecha { color: #666; font-size: 10px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #333; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .footer { margin-top: 20px; text-align: center; font-size: 10px; color: #666; }
                .filtros { background: #f0f0f0; padding: 10px; margin-bottom: 15px; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>' . $titulo . '</h1>
                <div class="fecha">Generado: ' . $fecha . '</div>
            </div>
            
            <div class="filtros">
                <strong>Filtros aplicados:</strong> ';
        
        if (!empty($filtros)) {
            foreach ($filtros as $key => $value) {
                if (!empty($value)) {
                    $html .= ucfirst($key) . ': ' . htmlspecialchars($value) . ' | ';
                }
            }
        } else {
            $html .= 'Sin filtros (todos los registros)';
        }
        
        $html .= '</div>
            
            <table>
                <thead>
                    <tr>';
        
        // Encabezados dinámicos
        if (!empty($datos)) {
            $primero = $datos[0];
            foreach (array_keys($primero) as $header) {
                $html .= '<th>' . ucwords(str_replace('_', ' ', $header)) . '</th>';
            }
        }
        
        $html .= '</tr>
                </thead>
                <tbody>';
        
        foreach ($datos as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell ?? '-') . '</td>';
            }
            $html .= '</tr>';
        }
        
        $html .= '</tbody>
            </table>
            
            <div class="footer">
                Sistema de Gestión de Inventario - SINTEMA HIGIENE
            </div>
        </body>
        </html>';
        
        return $html;
    }

    /**
     * Descarga el PDF
     */
    private function descargarPdf(string $html, string $filename): void {
        // Forzar descarga como HTML que puede ser impreso a PDF
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.html"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        echo $html;
        exit();
    }

    /**
     * Exporta a CSV
     */
    public function exportarCsv(string $tipo, array $filtros): void {
        if ($tipo === 'consumibles') {
            $datos = $this->reporteModel->getMovimientosConsumibles($filtros);
        } else {
            $datos = $this->reporteModel->getBienes($filtros);
        }
        
        if (empty($datos)) {
            die("No hay datos para exportar");
        }
        
        // Encabezados CSV
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_' . $tipo . '_' . date('Ymd_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        fputcsv($output, array_keys($datos[0]), ';');
        
        // Datos
        foreach ($datos as $row) {
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit();
    }
}
