<?php
class VotosController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        View::select(null, null);
    }

    // Registrar voto en un reporte
    public function votar($reporte_id, $valor)
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión para votar']);
            return;
        }

        $usuario_id = Auth::id();
        $reporte_id = (int) $reporte_id;
        $valor = (int) $valor;

        if ($valor !== 1 && $valor !== -1) {
            echo json_encode(['success' => false, 'message' => 'Valor inválido']);
            return;
        }

        $reporte = (new Reportes())->find_first($reporte_id);
        if (!$reporte) {
            echo json_encode(['success' => false, 'message' => 'Reporte no encontrado']);
            return;
        }

        $voto_existente = (new Votos())->find_first("conditions: reporte_id = $reporte_id AND usuario_id = $usuario_id");

        if ($voto_existente) {
            if ($voto_existente->valor == $valor) {
                $voto_existente->delete();
                $action = 'removed';
            } else {
                $voto_existente->valor = $valor;
                $voto_existente->update();
                $action = 'updated';
            }
        } else {
            $voto = new Votos();
            $voto->reporte_id = $reporte_id;
            $voto->usuario_id = $usuario_id;
            $voto->valor = $valor;

            if (!$voto->create()) {
                echo json_encode(['success' => false, 'message' => 'Error al guardar voto']);
                return;
            }
            $action = 'created';
        }

        $upvotes = (new Votos())->count("conditions: reporte_id = $reporte_id AND valor = 1");
        $downvotes = (new Votos())->count("conditions: reporte_id = $reporte_id AND valor = -1");
        $total = $upvotes - $downvotes;

        echo json_encode([
            'success' => true,
            'action' => $action,
            'total' => $total,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes
        ]);
    }

    // Obtener conteo de votos
    public function obtener($reporte_id)
    {
        header('Content-Type: application/json');

        $reporte_id = (int) $reporte_id;

        $upvotes = (new Votos())->count("conditions: reporte_id = $reporte_id AND valor = 1");
        $downvotes = (new Votos())->count("conditions: reporte_id = $reporte_id AND valor = -1");
        $total = $upvotes - $downvotes;

        echo json_encode([
            'success' => true,
            'total' => $total,
            'upvotes' => $upvotes,
            'downvotes' => $downvotes
        ]);
    }
}
