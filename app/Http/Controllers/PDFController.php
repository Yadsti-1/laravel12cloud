<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Models\Fecha;
use Carbon\Carbon;

class PDFController extends Controller
{
    public function procesarPDF(Request $request)
    {
        $request->validate(['pdf' => 'required|mimes:pdf|max:2048']);

        $pdfPath = $request->file('pdf')->store('pdfs');

        $parser = new Parser();
        $pdf = $parser->parseFile(storage_path('app/' . $pdfPath));
        $text = $pdf->getText();

        $this->guardarFechas($text);

        return redirect('/upload')->with('success', 'Calendario procesado y guardado.');
    }

    private function guardarFechas($text)
    {
        preg_match('/Calendario Tributario (\d{4})/', $text, $matches);
        $anio = $matches[1] ?? now()->year;

        $lineas = explode("\n", $text);
        $categoria = null;

        foreach ($lineas as $linea) {
            if (preg_match('/^Renta|IVA|PES|Activos en el exterior/i', $linea)) {
                $categoria = trim($linea);
            }

            if (preg_match('/(\w+)\s([\d-]+)\s([\d-]+)/', $linea, $matches)) {
                $mes = strtolower($matches[1]);
                $dias = explode('-', $matches[2]);
                $ultimo_digito_nit = $matches[3] ?? null;

                foreach ($dias as $dia) {
                    $fecha = Carbon::create($anio, $this->convertirMes($mes), $dia);

                    Fecha::create([
                        'categoria' => $categoria ?? 'Desconocida',
                        'mes' => ucfirst($mes),
                        'concepto' => 'Obligación tributaria',
                        'ultimo_digito_nit' => $ultimo_digito_nit,
                        'fecha' => $fecha,
                    ]);
                }
            }
        }
    }

    private function convertirMes($mes)
    {
        $meses = [
            'enero' => 1, 'febrero' => 2, 'marzo' => 3, 'abril' => 4,
            'mayo' => 5, 'junio' => 6, 'julio' => 7, 'agosto' => 8,
            'septiembre' => 9, 'octubre' => 10, 'noviembre' => 11, 'diciembre' => 12
        ];
        return $meses[$mes] ?? null;
    }
}
