<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Smalot\PdfParser\Parser;
use App\Models\Fecha;
use Carbon\Carbon;
use App\Models\Pdf;
use Illuminate\Support\Facades\Storage;

class UploadPdf extends Component
{
    use WithFileUploads;

    public $pdf;

    public function procesarPDF()
    {
        $this->validate([
            'pdf' => 'required|mimes:pdf|max:2048', // Validación del archivo
        ]);

        // Leer el contenido del PDF como binario
        $contenido = file_get_contents($this->pdf->getRealPath());

    // Guardar el PDF en la base de datos
        $pdf = Pdf::create([
            'nombre_original' => $this->pdf->getClientOriginalName(),
            'mime_type' => $this->pdf->getMimeType(),
            'contenido' => $contenido,
        ]);

        // Procesar el PDF desde la base de datos
        $parser = new Parser();
        $document = $parser->parseContent($pdf->contenido);
        $text = $document->getText();

        // Llamar a la función para extraer y guardar las fechas
        $this->guardarFechas($text);

        session()->flash('message', 'Calendario procesado y guardado.');
    }

    private function guardarFechas($text)
    {
        // Extraer el año desde el encabezado del PDF
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

    public function render()
    {
        return view('livewire.upload-pdf');
    }
}
