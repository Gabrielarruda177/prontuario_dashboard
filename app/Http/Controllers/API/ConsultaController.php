<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsultaController extends Controller
{
  
    public function index(Request $request)
    {
        $query = Consulta::with(['medico', 'enfermeiro', 'unidade']); 

        if ($request->has('paciente_id')) {
            $query->whereHas('prontuario', function ($q) use ($request) {
                $q->where('idPacienteFK', $request->paciente_id);
            });
        }

        return $query->orderBy('dataConsulta', 'desc')->paginate(15);
    }

 
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idProntuarioFK' => 'required|exists:tbProntuario,idProntuarioPK',
            'idMedicoFK' => 'required|exists:tbMedico,idMedicoPK',
            'idEnfermeiroFK' => 'nullable|exists:tbEnfermeiro,idEnfermeiroPK',
            'idUnidadeFK' => 'required|exists:tbUnidade,idUnidadePK',
            'dataConsulta' => 'required|date',
            'obsConsulta' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $consulta = Consulta::create($validator->validated());

        return response()->json($consulta, 201);
    }

    
    public function show($id)
    {
        
        $consulta = Consulta::with(['prontuario.paciente', 'medico', 'enfermeiro', 'unidade', 'medicamentos', 'exames'])->find($id);

        if (!$consulta) {
            return response()->json(['message' => 'Consulta não encontrada.'], 404);
        }

        return response()->json($consulta);
    }

   
    public function update(Request $request, $id)
    {
        $consulta = Consulta::find($id);
        if (!$consulta) {
            return response()->json(['message' => 'Consulta não encontrada.'], 404);
        }

        $data = $request->validate([
            'dataConsulta' => 'sometimes|date',
            'obsConsulta' => 'sometimes|nullable|string',
            
        ]);

        $consulta->update($data);

        return response()->json($consulta->fresh());
    }

    
    public function destroy($id)
    {
        $consulta = Consulta::find($id);
        if (!$consulta) {
            return response()->json(['message' => 'Consulta não encontrada.'], 404);
        }

        $consulta->delete();

        return response()->noContent(); 
    }
}
