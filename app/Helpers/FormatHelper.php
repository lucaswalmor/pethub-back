<?php

namespace App\Helpers;

/**
 * Classe auxiliar para formatação de dados
 * Contém métodos estáticos para formatar diferentes tipos de dados
 */
class FormatHelper
{
    /**
     * Formatar CEP no padrão brasileiro
     * Exemplo: 30130000 -> 30130-000
     */
    public static function formatCep(string $cep): string
    {
        return preg_replace('/^(\d{5})(\d{3})$/', '$1-$2', $cep);
    }

    /**
     * Formatar CPF no padrão brasileiro
     * Exemplo: 12345678901 -> 123.456.789-01
     */
    public static function formatCpf(string $cpf): string
    {
        return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $cpf);
    }

    /**
     * Formatar CNPJ no padrão brasileiro
     * Exemplo: 12345678000190 -> 12.345.678/0001-90
     */
    public static function formatCnpj(string $cnpj): string
    {
        return preg_replace('/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cnpj);
    }

    /**
     * Formatar telefone no padrão brasileiro
     * Exemplo: 34992021394 -> (34) 99202-1394
     */
    public static function formatTelefone(string $telefone): string
    {
        return preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $telefone);
    }

    /**
     * Formatar telefone removendo apenas números (sem formatação)
     * Remove todos os caracteres especiais, mantendo apenas números
     * Exemplo: (34) 9 9202-1394 -> 34992021394
     */
    public static function formatOnlyNumbers(string $telefone): string
    {
        return preg_replace('/\D/', '', $telefone);
    }

    /**
     * Formatar texto para slug (URL amigável)
     * Converte texto em slug removendo acentos, caracteres especiais e espaços
     * Exemplo: "PetShop do João & Cia" -> "petshop-do-joao-cia"
     */
    public static function formatSlug(string $text): string
    {
        // Converte para minúsculas
        $text = mb_strtolower($text, 'UTF-8');

        // Remove acentos e caracteres especiais
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);

        // Remove caracteres que não são letras, números ou espaços
        $text = preg_replace('/[^a-z0-9\s]/', '', $text);

        // Troca espaços múltiplos por um único espaço
        $text = preg_replace('/\s+/', ' ', $text);

        // Troca espaços por hífens
        $text = str_replace(' ', '-', trim($text));

        // Remove hífens múltiplos
        $text = preg_replace('/-+/', '-', $text);

        // Remove hífens do início e fim
        return trim($text, '-');
    }
}
