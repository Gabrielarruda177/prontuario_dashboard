<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\Enfermeiro; // ✅ Certifique-se de que este model existe
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Contagens para os cards
        $adminCount = Medico::count();
        $patientsCount = Paciente::count();
        $pendingExamsCount = 0; // Placeholder
        $nursesCount = Enfermeiro::count(); // ✅ NOVO: Contagem de enfermeiros

        // Médicos por especialidade
        $medicosPorEspecialidade = DB::table('tbMedico')
            ->select('especialidadeMedico', DB::raw('count(*) as total'))
            ->whereNotNull('especialidadeMedico')
            ->groupBy('especialidadeMedico')
            ->orderBy('total', 'desc')
            ->get();

        // 📊 Crescimento de Admins e Pacientes por mês (Últimos 6 meses)
        $dadosLinha = [
            'meses' => [],
            'admins' => [],
            'pacientes' => [],
        ];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);
            $dadosLinha['meses'][] = $mes->format('M Y'); // Ex: Jan 2024
            
            $dadosLinha['admins'][] = Medico::whereYear('dataCadastroMedico', $mes->year)
                ->whereMonth('dataCadastroMedico', $mes->month)
                ->count();
            
            $dadosLinha['pacientes'][] = Paciente::whereYear('created_at', $mes->year)
                ->whereMonth('created_at', $mes->month)
                ->count();
        }

        // 📊 Distribuição de gênero (Homens, Mulheres, Idosos)
        $homens = Paciente::where('genero', 'Masculino')->count();
        $mulheres = Paciente::where('genero', 'Feminino')->count();
        $idosos = Paciente::where('data_nasc', '<=', Carbon::now()->subYears(60)->toDateString())->count();

        $dadosGenero = [
            'Homens'   => $homens,
            'Mulheres' => $mulheres,
            'Idosos'   => $idosos,
        ];

        // ✅ Enviando também $nursesCount para a view
        return view('admin.dashboard', compact(
            'adminCount',
            'patientsCount',
            'pendingExamsCount',
            'nursesCount',
            'medicosPorEspecialidade',
            'dadosLinha',
            'dadosGenero'
        ));
    }
<<<<<<< HEAD
}
=======
}
<<<<<<< HEAD
=======

>>>>>>> 994bc1bc5c0130793e5eeb04f4803ac8586ca5ed
>>>>>>> b05724b54f50b674a5f40d03a034b569757c6cfc
