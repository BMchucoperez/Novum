<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistInspection extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'owner_id',
        'vessel_id',
        'inspection_start_date',
        'inspection_end_date',
        'convoy_date',
        'inspector_name',
        'parte_1_items',
        'parte_2_items',
        'parte_3_items',
        'parte_4_items',
        'parte_5_items',
        'parte_6_items',
        'overall_status',
        'general_observations',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'inspection_start_date' => 'date',
        'inspection_end_date' => 'date',
        'convoy_date' => 'date',
        'parte_1_items' => 'array',
        'parte_2_items' => 'array',
        'parte_3_items' => 'array',
        'parte_4_items' => 'array',
        'parte_5_items' => 'array',
        'parte_6_items' => 'array',
    ];

    /**
     * Get the vessel that owns the checklist inspection.
     */
    public function vessel(): BelongsTo
    {
        return $this->belongsTo(Vessel::class);
    }

    /**
     * Get the owner that owns the checklist inspection.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class);
    }

    /**
     * Get the default structure for each part with checklist format
     */
    public static function getDefaultStructure(): array
    {
        return [
            'parte_1' => [
                ['item' => 'Certificado nacional de arqueação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Declaração de conformidade para transporte de petróleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado de registro de armador (CRA)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Livro de oleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de segurança', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de rede de carga e descarga', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de caoacidade de tanques', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Teste de Opacidade', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => 'Certificado de teste pneumático dos tanques de armazenamento de óleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da rede de carga / descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da válvula de pressão e vácuo ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e matrícula/número de registro pintados em ambos bordos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula pintados na popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão identificados com sua capacidade de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam aberturas e escotilhas padronizadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões principais da embarcação (L, B, D) estão conforme seus certificados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sobreposição em reparos estruturais?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿Os elementos estruturais apresentam continuidade estrutural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A sequência de soldagem nos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem no chapeamento, elementos estruturais, conexões e acessórios está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem apresenta defeitos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões entre chapas e entre elementos estruturais estão alinhadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿A instalação dos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos enxertos no chapeamento e nos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura da embarcação apresenta elementos concentradores de tensões?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura apresenta todos os seus elementos estruturais instalados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿Os tanques de carga são herméticos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga apresentam reparos de acordo com o padrão naval?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barra redonda de aço, verduguete ou cinta de atrito (proteção do casco) encontra-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bombas de descarga possuem sistema de parada de emergência remota claramente identificado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿Os motores das bombas estão operacionais, com proteções adequadas e escapes totalmente isolados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor da bomba apresenta tela corta-chamas (antifaísca) e uma caixa inibidora de gases com seu visor de nível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barreira de contenção possui tampões de embornais herméticos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿O processo de soldagem da barreira de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O quadro de controle do sistema de alarme encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias do sistema de alarme de nivel encontram-se em boas condições e estão guardadas em caixa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente pintados com o nome e matrícula ou nº de registro da embarcação, e contam com uma base adequada?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As máquinas e peças móveis a bordo estão protegidas com dispositivos de proteção eficazes para evitar acidentes?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) do convés encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Bombordo – Vermelha 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Boreste – Verde 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Topo – Branca 225° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com a norma?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A base das luzes de navegação encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿As escadas de acesso ao convés de carga encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_6' => [
                ['item' => '¿Conta-se com cabeços de amarração duplos ou simples, devidamente posicionados na proa e na popa?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta-se com cunhos ou cabeços e amarração simples adicionais, distribuídos em ambos bordos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços e cunhos estão em boas condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => ''],
                ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com olhais soldados ao casco, em ambos os bordos, para o trincamento das defensas durante as operações em terminais ou pontos de embarque?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os conectores das extremidades dos cabos e suas emendas de olho encontram-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As emendas de olho cumprem o padrão das indústrias de segurança (número mínimo de grampos por diâmetro de cabo)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
        ];
    }

    /**
     * Get status options
     */
    public static function getStatusOptions(): array
    {
        return [
            'V' => 'V - Vigente (100% operativo, cumple, buenas condiciones)',
            'A' => 'A - En trámite (operativo con observaciones menores)',
            'N' => 'N - Reparaciones (observaciones que comprometen estanqueidad)',
            'R' => 'R - Vencido (inoperativo, no cumple, observaciones críticas)',
        ];
    }

    /**
     * Get status colors for badges
     */
    public static function getStatusColors(): array
    {
        return [
            'V' => 'success',    // Verde
            'A' => 'warning',    // Amarillo
            'N' => 'danger',     // Naranja (usando danger como aproximación)
            'R' => 'danger',     // Rojo
        ];
    }

    /**
     * Get overall status options
     */
    public static function getOverallStatusOptions(): array
    {
        return [
            'V' => 'V - Conforme General',
            'A' => 'A - Conforme con Observaciones',
            'N' => 'N - No Conforme con Reparaciones',
            'R' => 'R - No Conforme Crítico',
        ];
    }

    /**
     * Get priority options
     */
    public static function getPriorityOptions(): array
    {
        return [
            1 => 'Prioridad 1 - Crítica',
            2 => 'Prioridad 2 - Alta',
            3 => 'Prioridad 3 - Media',
        ];
    }

    /**
     * Get priority colors for badges
     */
    public static function getPriorityColors(): array
    {
        return [
            1 => 'danger',    // Rojo - Crítica
            2 => 'warning',   // Amarillo - Alta
            3 => 'success',   // Verde - Media
        ];
    }

    /**
     * Check if priority allows file attachments
     */
    public static function priorityAllowsAttachments(int $priority): bool
    {
        return in_array($priority, [1, 2]);
    }

    /**
     * Calcula el estado general automáticamente según los estados de todos los ítems de todas las partes.
     */
    public function calculateOverallStatus(): string
    {
        $allEstados = [];
        for ($i = 1; $i <= 6; $i++) {
            $items = $this->getAttribute('parte_' . $i . '_items') ?? [];
            foreach ($items as $item) {
                if (!empty($item['estado'])) {
                    $allEstados[] = $item['estado'];
                }
            }
        }
        if (empty($allEstados)) {
            return 'A'; // Por defecto si no hay estados
        }
        if (in_array('R', $allEstados, true)) {
            return 'R';
        }
        if (in_array('N', $allEstados, true)) {
            return 'N';
        }
        if (in_array('A', $allEstados, true)) {
            return 'A';
        }
        return 'V';
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->overall_status = $model->calculateOverallStatus();
        });
    }
}
