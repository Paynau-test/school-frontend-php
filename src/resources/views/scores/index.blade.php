@extends('layouts.app')

@section('title', 'Calificaciones')

@section('content')
<div class="row">
    {{-- ── Search form ── --}}
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-3">Consultar Calificaciones</h5>
                <form method="GET" action="/scores" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Alumno (buscar por ID o nombre)</label>
                        <select name="studentId" id="studentSelect" class="form-select" required>
                            <option value="">Escriba ID o nombre del alumno...</option>
                            @foreach($students as $student)
                                <option value="{{ $student['id'] }}"
                                    data-grade="{{ $student['gradeId'] }}"
                                    {{ $filters['studentId'] == $student['id'] ? 'selected' : '' }}>
                                    #{{ $student['id'] }} — {{ $student['lastNameFather'] }} {{ $student['lastNameMother'] }}, {{ $student['firstName'] }}
                                    ({{ ucfirst($student['gradeName']) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Grado</label>
                        <select name="gradeId" class="form-select" required>
                            <option value="">-- Seleccionar grado --</option>
                            @foreach($grades as $grade)
                                <option value="{{ $grade['id'] }}"
                                    {{ $filters['gradeId'] == $grade['id'] ? 'selected' : '' }}>
                                    {{ ucfirst($grade['name']) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Mes</label>
                        <select name="month" class="form-select">
                            @php
                                $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                                          'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
                            @endphp
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ (int)$filters['month'] === $m ? 'selected' : '' }}>
                                    {{ $meses[$m - 1] }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Año</label>
                        <input type="number" name="year" class="form-control"
                               value="{{ $filters['year'] }}" min="2020" max="2030">
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
                    @if($selectedStudent)
                        {{ $selectedStudent['firstName'] }} {{ $selectedStudent['lastNameFather'] }} {{ $selectedStudent['lastNameMother'] }}
                    @else
                        Alumno #{{ $filters['studentId'] }}
                    @endif
                    <span class="text-muted fs-6">—
                        @foreach($grades as $g)
                            @if($g['id'] == $filters['gradeId']) {{ ucfirst($g['name']) }} @endif
                        @endforeach
                        · {{ $meses[(int)$filters['month'] - 1] }} {{ $filters['year'] }}
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
                                    <form method="POST" action="/scores">
                                        @csrf
                                        <input type="hidden" name="studentId" value="{{ $filters['studentId'] }}">
                                        <input type="hidden" name="subjectId" value="{{ $score['subjectId'] }}">
                                        <input type="hidden" name="gradeId" value="{{ $filters['gradeId'] }}">
                                        <input type="hidden" name="year" value="{{ $filters['year'] }}">
                                        <input type="hidden" name="month" value="{{ $filters['month'] }}">

                                        <td><strong>{{ $score['subjectName'] }}</strong></td>
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
    $(document).ready(function() {
        // Select2 on student dropdown
        $('#studentSelect').select2({
            theme: 'bootstrap-5',
            placeholder: 'Escriba ID o nombre del alumno...',
            width: '100%'
        });

        // Auto-select grade when student changes
        $('#studentSelect').on('change', function() {
            var selected = $(this).find(':selected');
            var gradeId = selected.data('grade');
            if (gradeId) {
                $('select[name="gradeId"]').val(gradeId);
            }
        });
    });

    // Validate score 0-10
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
