<?php

namespace App\Models;

class VesselDocumentType
{
    // DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO
    const CERTIFICADO_ARQUEACAO = 'certificado_nacional_arqueacao';
    const CERTIFICADO_BORDA_LIVRE = 'certificado_borda_livre_navegacao_interior';
    const PROVISAO_REGISTRO = 'provisao_registro_propriedade_maritima';
    const CERTIFICADO_SEGURANCA = 'certificado_seguranca_navegacao';
    const LICENCA_IPAAM = 'licenca_operacao_ipaam';
    const AUTORIZACAO_ANP = 'autorizacao_anp';
    const AUTORIZACAO_ANTAQ = 'autorizacao_antaq';
    const AUTORIZACAO_IBAMA = 'autorizacao_ambiental_ibama';
    const CERTIFICADO_REGULARIDADE = 'certificado_regularidade_ibama';
    const CERTIFICADO_ARMADOR = 'certificado_registro_armador_cra';
    const APOLICE_SEGURO = 'apolice_seguro_pi';

    // DOCUMENTOS EXCLUSIVOS PARA BARCAZAS
    const DECLARACAO_CONFORMIDADE = 'barcaza_declaracao_conformidade_transporte_petroleo';

    // DOCUMENTOS EXCLUSIVOS PARA EMPUJADORES
    const CARTAO_TRIPULACAO = 'empujador_cartao_tripulacao_seguranca_cts';
    const LICENCA_ESTACAO = 'empujador_licenca_estacao_navio';
    const CERTIFICADO_CONTROLE = 'certificado_de_controle_de_praga';
    const PLANO_INCENDIO = 'plano_de_incendio';
    const OPERADOR_TECNICO = 'operador_tecnico';
    const CREW_LIST = 'crew_list';

    // DOCUMENTOS EXCLUSIVOS PARA MOTOCHATAS
    const MOTOCHATA_DOCUMENTO_1 = 'motochata_documento_especial_1';
    const MOTOCHATA_DOCUMENTO_2 = 'motochata_documento_especial_2';

    // DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO
    const LIVRO_OLEO = 'livro_oleo';
    const PLANO_SEGURANCA = 'plano_seguranca';
    const PLANO_ARRANJO = 'plano_arranjo_geral';
    const PLANO_REDE_CARGA = 'plano_rede_carga_descarga';
    const PLANO_CAPACIDADE = 'plano_capacidade_tanques';
    const PLANO_CONTINGENCIA = 'plano_contingencia';
    const CERTIFICADO_PNEUMATICO = 'certificado_teste_pneumatico_tanques';
    const CERTIFICADO_REDE = 'certificado_teste_rede_carga_descarga';
    const CERTIFICADO_VALVULA = 'certificado_teste_valvula_pressao_vacuo';
    const PLANO_SOPEP = 'plano_emergencia_sopep';
    const CERTIFICADO_EXTINTORES = 'certificado_teste_hidros_extintores';

    /**
     * Obtener documentos por categoría BANDEIRA E APOLICES DE SEGURO
     */
    public static function getBandeiraApolicesDocuments(): array
    {
        return [
            self::CERTIFICADO_ARQUEACAO => 'Certificado nacional de arqueação',
            self::CERTIFICADO_BORDA_LIVRE => 'Certificado nacional de borda livre para a navegação interior',
            self::PROVISAO_REGISTRO => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)',
            self::CERTIFICADO_SEGURANCA => 'Certificado de segurança de navegação',
            self::LICENCA_IPAAM => 'Licença de operação - IPAAM',
            self::AUTORIZACAO_ANP => 'Autorização de ANP',
            self::AUTORIZACAO_ANTAQ => 'Autorização de ANTAQ',
            self::AUTORIZACAO_IBAMA => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA',
            self::CERTIFICADO_REGULARIDADE => 'Certificado de regularidade - IBAMA',
            self::CERTIFICADO_ARMADOR => 'Certificado de registro de armador (CRA)',
            self::APOLICE_SEGURO => 'Apolice de seguro P&I',
        ];
    }

    /**
     * Obtener documentos exclusivos para Barcazas
     */
    public static function getBarcazaExclusiveDocuments(): array
    {
        return [
            self::DECLARACAO_CONFORMIDADE => 'Declaração de conformidade para transporte de petróleo',
            self::PLANO_REDE_CARGA => 'Plano de rede de carga e descarga',
            self::PLANO_CAPACIDADE => 'Plano de caoacidade de tanques',
            self::PLANO_CONTINGENCIA => 'Plano de contingência',
            self::CERTIFICADO_PNEUMATICO => 'Certificado de teste pneumático dos tanques de armazenamento de óleo',
            self::CERTIFICADO_REDE => 'Certificado de Teste da rede de carga / descarga',
            self::CERTIFICADO_VALVULA => 'Certificado de Teste da válvula de pressão e vácuo',
            self::PLANO_SOPEP => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP',
        ];
    }

    /**
     * Obtener documentos exclusivos para Empujadores
     */
    public static function getEmpujadorExclusiveDocuments(): array
    {
        return [
            self::CARTAO_TRIPULACAO => 'Cartão de tripulação de segurança (CTS)',
            self::LICENCA_ESTACAO => 'Licença de estação de navio',
            self::CERTIFICADO_CONTROLE => 'Certificado de controle de Praga',
            self::PLANO_INCENDIO => 'Plano de incêndio',
            self::OPERADOR_TECNICO => 'Operador técnico',
            self::CREW_LIST => 'Crew List',
        ];
    }

    /**
     * Obtener documentos exclusivos para Motochatas
     */
    public static function getMotochataExclusiveDocuments(): array
    {
        return [
            self::MOTOCHATA_DOCUMENTO_1 => 'Documento especial motochata 1',
            self::MOTOCHATA_DOCUMENTO_2 => 'Documento especial motochata 2',
        ];
    }

    /**
     * Obtener documentos por categoría SISTEMA DE GESTÃO DE BORDO
     */
    public static function getSistemaGestaoDocuments(): array
    {
        return [
            self::PLANO_SEGURANCA => 'Plano de segurança',
            self::PLANO_ARRANJO => 'Plano de arranjo geral',
            self::CERTIFICADO_EXTINTORES => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio',
        ];
    }

    /**
     * Obtener todos los tipos de documentos
     */
    public static function getAllDocuments(): array
    {
        return array_merge(
            self::getBandeiraApolicesDocuments(),
            self::getSistemaGestaoDocuments(),
            self::getBarcazaExclusiveDocuments(),
            self::getEmpujadorExclusiveDocuments(),
            self::getMotochataExclusiveDocuments()
        );
    }

    /**
     * Obtener categoría por tipo de documento
     */
    public static function getCategoryByType(string $documentType): string
    {
        if (array_key_exists($documentType, self::getBandeiraApolicesDocuments())) {
            return 'bandeira_apolices';
        }
        
        if (array_key_exists($documentType, self::getSistemaGestaoDocuments())) {
            return 'sistema_gestao';
        }

        if (array_key_exists($documentType, self::getBarcazaExclusiveDocuments())) {
            return 'barcaza_exclusive';
        }

        if (array_key_exists($documentType, self::getEmpujadorExclusiveDocuments())) {
            return 'empujador_exclusive';
        }

        if (array_key_exists($documentType, self::getMotochataExclusiveDocuments())) {
            return 'motochata_exclusive';
        }
        
        throw new \InvalidArgumentException("Tipo de documento no válido: {$documentType}");
    }

    /**
     * Obtener documentos exclusivos por tipo de embarcación
     */
    public static function getExclusiveDocumentsByVesselType(string $vesselType): array
    {
        switch (strtolower($vesselType)) {
            case 'barcaza':
                return self::getBarcazaExclusiveDocuments();
            case 'empujador':
                return self::getEmpujadorExclusiveDocuments();
            case 'motochata':
                return self::getMotochataExclusiveDocuments();
            default:
                return [];
        }
    }

    /**
     * Verificar si un tipo de documento es válido
     */
    public static function isValidType(string $documentType): bool
    {
        return array_key_exists($documentType, self::getAllDocuments());
    }
}