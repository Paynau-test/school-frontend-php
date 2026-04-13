@extends('layouts.app')

@section('title', 'Calificaciones')

@section('content')
<div class="row">
    {{-- ── Search form ── --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Buscar Calificaciones</h5>
                <form method="GET" action="/scores" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">ID Alumno</label>
                        <input type="number" name="studentId" class="form-control"
                               value="{{ $filters['studentId'] }}" min="1" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grado</label>
                        <input type="number" name="gradeId" class="form-control"
                               value="{{ $filters['gradeId'] }}" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Año</label>
                        <input type="number" name="year" class="form-control"
                               value="{{ $filters['year'] }}" min="2020" max="2030">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mes</label>
                        <select name="month" class="form-select">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int)$filters['month'] === $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Error ── --}}
    @if($error)
        <div class="col-12 mb-3">
            <div class="alert alert-danger">{{ $error }}</div>
        </div>
    @endif

    {{-- ── Results ── --}}
    @if($searched && !$error)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    Materias del Alumno #{{ $filters['studentId'] }}
                    <span class="text-muted fs-6">— Grado {{ $filters['gradeId'] }},
                        {{ DateTime::createFromFormat('!m', $filters['month'])->format('F') }} {{ $filters['year'] }}
                    </span>
                </h5>

                @if(count($scores) === 0)
                    <p class="text-muted">No se encontraron materias para los filtros seleccionados.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Materia</th>
                                    <th class="text-center" style="width:140px">Calificación</th>
                                    <th class="text-center" style="width:120px">Estado</th>
                                    <th class="text-center" style="width:100px">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scores as $score)
                                <tr>
                                    <form method="POST" action="/scores" class="score-form">
                                        @csrf
                                        <input type="hidden" name="studentId" value="{{ $filters['studentId'] }}">
                                        <input type="hidden" name="subjectId" value="{{ $score['subjectId'] }}">
                                        <input type="hidden" name="gradeId" value="{{ $filters['gradeId'] }}">
                                        <input type="hidden" name="year" value="{{ $filters['year'] }}">
                                        <input type="hidden" name="month" value="{{ $filters['month'] }}">

                                        <td>
                                            <strong>{{ $score['subjectName'] }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <input type="number" name="score"
                                                   class="form-control score-input mx-auto"
                                                   value="{{ $score['score'] ?? '' }}"
                                                   min="0" max="10" step="0.1"
                                                   placeholder="0-10" required>
                                        </td>
                                        <td class="text-center">
                                            @if($score['isRecorded'] ?? false)
                                                <span class="badge badge-saved">Capturada</span>
                                            @else
                                                <span class="badge badge-pending">Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                Guardar
                                            </button>
                                        </td>
                                    </form>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Validate score 0-10 on input
    document.querySelectorAll('.score-input').forEach(input => {
        input.addEventListener('change', function() {
            let val = parseFloat(this.value);
            if (isNaN(val)) return;
            if (val < 0) this.value = 0;
            if (val > 10) this.value = 10;
            this.value = Math.round(val * 10) / 10;
        });
    });
</script>
@endpush
@endsection
