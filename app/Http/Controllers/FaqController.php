<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faq;

class FaqController extends Controller
{
    /**
     * Listar todas as FAQs ativas
     */
    public function index(Request $request)
    {
        $query = Faq::ativo()->ordenado();

        // Filtro por categoria
        if ($request->has('categoria') && !empty($request->categoria)) {
            $query->porCategoria($request->categoria);
        }

        $faqs = $query->get();

        // Agrupar por categoria
        $faqsPorCategoria = $faqs->groupBy('categoria')->map(function ($items, $categoria) {
            return [
                'categoria' => $categoria,
                'faqs' => $items->map(function ($faq) {
                    return [
                        'id' => $faq->id,
                        'pergunta' => $faq->pergunta,
                        'resposta' => $faq->resposta,
                    ];
                })
            ];
        })->values();

        return response()->json([
            'success' => true,
            'faqs' => $faqsPorCategoria,
            'categorias' => $faqs->pluck('categoria')->unique()->values()
        ]);
    }

    /**
     * Buscar FAQs por palavra-chave
     */
    public function buscar(Request $request)
    {
        $query = $request->input('q', '');

        if (empty(trim($query))) {
            return response()->json([
                'success' => false,
                'message' => 'Digite uma palavra-chave para buscar'
            ], 400);
        }

        $faqs = Faq::ativo()
            ->where(function ($q) use ($query) {
                $q->where('pergunta', 'like', '%' . $query . '%')
                  ->orWhere('resposta', 'like', '%' . $query . '%');
            })
            ->ordenado()
            ->get();

        return response()->json([
            'success' => true,
            'total' => $faqs->count(),
            'faqs' => $faqs->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'categoria' => $faq->categoria,
                    'pergunta' => $faq->pergunta,
                    'resposta' => $faq->resposta,
                ];
            })
        ]);
    }
}
