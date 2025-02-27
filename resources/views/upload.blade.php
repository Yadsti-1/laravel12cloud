<form action="{{ route('procesar.pdf') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="pdf" accept="application/pdf" required>
    <button type="submit">Subir y Procesar</button>
</form>