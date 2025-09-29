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
     * Get structure specific for Barcaza type vessels
     */
    public static function getBarcazaStructure(): array
    {
        return [
            'parte_1' => [
                ['item' => 'Certificado nacional de arqueação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Declaração de conformidade para transporte de petróleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Plano de segurança', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                
                ['item' => 'Plano de rede de carga e descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de caoacidade de tanques', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de teste pneumático dos tanques de armazenamento de óleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da rede de carga / descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da válvula de pressão e vácuo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de contingência', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de registro pintados em ambos bordos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula pintados na popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão identificados com sua capacidade de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reforços estruturais apresentam continuidade estrutural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A sequência de soldagem nos reforços estruturais apresenta resistência e estabilidade?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem no chapeamento, reforços estruturais e conexões apresenta continuidade e resistência?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As uniões soldadas apresentam fissuras, porosidade, escavação ou outros defeitos de execução?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As uniões entre chapa-chapa e reforço-reforço estão alinhadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos enxertos no chapamento e nos reforços estruturais garantem sua resistência e segurança?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura da embarcação conta com todos os seus reforços estruturais completos e corretamente instalados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos reforços estruturais são adequadas para garantir a resistência e a segurança da embarcação?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga são herméticos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reparos nos tanques de carga apresentam qualidade estrutural?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bombas de descarga possuem sistema de parada de emergência remota claramente identificado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O motor da bomba e a própria bomba estão em bom estado e operacionais?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta com braço de carga para as manobras de içamento e arriamento do mangote, e encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor da bomba apresenta tela corta-chamas (antifaísca) e uma caixa inibidora de gases com seu visor de nível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barreira de contenção possui tampões de embornais herméticos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem da barreira de contenção do convés apresenta continuidade, integridade e acabamento adequados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O quadro de controle do sistema de alarme encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias encontram-se em bom estado, instaladas em caixa de segurança e com terminais devidamente protegidos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente identificados com o nome e porto de registro, e possuem uma base adequada para sua estiva?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As máquinas e peças móveis a bordo estão providas de proteções ou coberturas de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) do convés encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Bombordo – Vermelha em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Boreste – Verde em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Topo – Branca em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor está corretamente isolado em toda a sua extensão?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A base das luzes de navegação encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As escadas de acesso ao convés de carga encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_6' => [
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação dispõe em ambas as bandas de defensas em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
        ];
    }

    /**
     * Get structure specific for Empujador type vessels
     */
    public static function getEmpujadorStructure(): array
    {
        return [
            'parte_1' => [
                ['item' => 'Certificado nacional de arqueação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Cartão de tripulação de segurança (CTS)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de estação de navio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Plano de segurança', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                
                ['item' => 'Certificado de controle de Praga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de incêndio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Operador técnico', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Crew List', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de combustível e conformidade apresentam corrosão significativa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de registro rotulados em ambas as bandas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula rotulados na popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas visíveis do disco de Plimsoll em ambas as bandas, de acordo com o certificado de linha máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas visíveis de calado e escalas correspondentes na proa, popa e seção média de ambos os costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão rotulados com sua capacidade de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, planchadas e elementos estruturais estão livres de amassados/deformações significativas?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reforços estruturais apresentam continuidade estrutural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem do casco e da estrutura encontra-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, planchadas, elementos estruturais e conexões apresentam fissuras, rupturas (furos)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de ventilação, sondagem e escotilhas estão em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As defensas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os motores de propulsão possuem controles desde a ponte e sala de máquinas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os motores de propulsão estão em bom estado e operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O botão de parada de emergência está operacional?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com sistema de governo principal e auxiliar em condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui na ponte de comando um indicador de ângulo de pala (axiómetro) em bom estado de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O tanque de óleo hidráulico está em bom estado e possui visor de nível adequado e tubulação de ventilação?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As prensa-estopas dos sistemas de governo e propulsão estão corretamente instaladas e garantem estanqueidade?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de combustível possuem visor de nível adequado, tubulação de ventilação com proteção contra chamas (arresta-chamas) e tubulação de enchimento com bandeja anti-derrame?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de combustível, juntamente com suas tubulações de ventilação, enchimento e tampas de registro, estão rotulados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com grupos geradores principal e auxiliar em condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os quadros elétricos e a rede de alimentação encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistemas de água doce, serviços gerais e dejeto em bom estado de funcionamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O tanque de água doce e os serviços gerais estão em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistemas de esgoto dos compartimentos vazios e porão da sala de máquinas em bom estado de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta alarme de porão (sonoro) de nível alto na sala de máquinas em condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta com tanques de retenção do sistema de sentina e do sistema de águas negras?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Apresenta teste de opacidade?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui diário de navegação?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui diário de máquinas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Ao menos um tripulante deve estar familiarizado com os equipamentos de rádio?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A iluminação interna e externa do navio é adequada e encontra-se em bom estado de funcionamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistema de iluminação de emergência na sala de máquinas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As luzes de navegação estão operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta os seguintes equipamentos de comunicação em bom estado?: * Uma (01) Rádio VHF (operando no canal 16) * Uma (01) Rádio HF (operando no canal 5850 ou outra frequência fluvial estabelecida)   ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta os seguintes equipamentos de navegação em bom estado?: * Um (01) Olofote * Um (01) GPS * Uma (01) ecosonda * Um (01) radar ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias dos equipamentos de comunicação e navegação encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente rotulados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas possuem corda, guirlanda e fita refletiva adequadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas possuem luz, apito, faixas refletivas e estão marcados com o nome do barco e seu porto de registro?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A quantidade de anéis salva-vidas e extintores portáteis está de acordo com o plano de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui sinalização de advertência visível em áreas de risco: "Perigo Combustível" e "Proibido Fumar"?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os produtos inflamáveis encontram-se em áreas seguras?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os artefatos de gás e tubulações estão corretamente estibados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui botão geral de alarme de emergência (sonoro) em bom estado de funcionamento e pelo menos um na ponte de comando?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O convés e a sala de máquinas estão corretamente iluminadas, ventiladas e livres de obstáculos (limpas e organizadas)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O navio possui embarcação auxiliar?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui kit de primeiros socorros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de combate a incêndio possui bomba exclusiva, manômetro, hidrantes, mangueiras e acoplamentos de abertura rápida em bom estado e estão operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) da coberta e superestrutura está em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O braço de carga com guincho (manual e/ou elétrico) encontra-se operacional e em condições seguras de uso?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bitas duplas, simples e/ou cornamusas estão instaladas em número suficiente e corretamente distribuídas ao longo da embarcação?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Dispõe de defensas adequadas para as manobras de empuxo e atracação?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_6' => static::getCommonParte6(),
        ];
    }

    /**
     * Get structure specific for Motochata type vessels
     */
    public static function getMotochataStructure(): array
    {
        return [
            'parte_1' => [
                ['item' => 'Certificado nacional de arqueação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Cartão de tripulação de segurança (CTS)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de estação de navio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Plano de segurança', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                
                ['item' => 'Certificado de controle de Praga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de incêndio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Operador técnico', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Crew List', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => static::getCommonParte3(),
            'parte_4' => static::getCommonParte4(),
            'parte_5' => static::getCommonParte5(),
            'parte_6' => static::getCommonParte6(),
        ];
    }

    /**
     * Get the default structure for each part with checklist format
     * @param string|null $vesselType Tipo de embarcación (barcaza, empujador, motochata)
     */
    public static function getDefaultStructure(?string $vesselType = null): array
    {
        // Si se especifica un tipo de embarcación, usar estructura específica
        switch (strtolower($vesselType ?? '')) {
            case 'barcaza':
                return static::getBarcazaStructure();
            case 'empujador':
                return static::getEmpujadorStructure();
            case 'motochata':
                return static::getMotochataStructure();
            default:
                // Estructura por defecto (actual)
                break;
        }
        return [
            'parte_1' => [
                ['item' => 'Certificado nacional de arqueação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Declaração de conformidade para transporte de petróleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Livro de oleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de segurança', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de rede de carga e descarga', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de caoacidade de tanques', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Teste de Opacidade', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de teste pneumático dos tanques de armazenamento de óleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da rede de carga / descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da válvula de pressão e vácuo ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e matrícula/número de registro pintados em ambos bordos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula pintados na popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão identificados com sua capacidade de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam aberturas e escotilhas padronizadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões principais da embarcação (L, B, D) estão conforme seus certificados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sobreposição em reparos estruturais?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os elementos estruturais apresentam continuidade estrutural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A sequência de soldagem nos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem no chapeamento, elementos estruturais, conexões e acessórios está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem apresenta defeitos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões entre chapas e entre elementos estruturais estão alinhadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A instalação dos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos enxertos no chapeamento e nos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura da embarcação apresenta elementos concentradores de tensões?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura apresenta todos os seus elementos estruturais instalados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga são herméticos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga apresentam reparos de acordo com o padrão naval?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barra redonda de aço, verduguete ou cinta de atrito (proteção do casco) encontra-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bombas de descarga possuem sistema de parada de emergência remota claramente identificado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os motores das bombas estão operacionais, com proteções adequadas e escapes totalmente isolados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor da bomba apresenta tela corta-chamas (antifaísca) e uma caixa inibidora de gases com seu visor de nível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barreira de contenção possui tampões de embornais herméticos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem da barreira de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O quadro de controle do sistema de alarme encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias do sistema de alarme de nivel encontram-se em boas condições e estão guardadas em caixa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente pintados com o nome e matrícula ou nº de registro da embarcação, e contam com uma base adequada?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As máquinas e peças móveis a bordo estão protegidas com dispositivos de proteção eficazes para evitar acidentes?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) do convés encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Bombordo – Vermelha 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Boreste – Verde 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Topo – Branca 225° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com a norma?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A base das luzes de navegação encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As escadas de acesso ao convés de carga encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_6' => [
                ['item' => '¿Conta-se com cabeços de amarração duplos ou simples, devidamente posicionados na proa e na popa?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta-se com cunhos ou cabeços e amarração simples adicionais, distribuídos em ambos bordos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços e cunhos estão em boas condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
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
            'APTO' => 'APTO - Cumple con los requisitos',
            'NO APTO' => 'NO APTO - No cumple (Prioridad 1)',
            'OBSERVADO' => 'OBSERVADO - No cumple (Prioridad 2-3)',
        ];
    }

    /**
     * Get status colors for badges
     */
    public static function getStatusColors(): array
    {
        return [
            'APTO' => 'success',        // Verde
            'NO APTO' => 'danger',      // Rojo
            'OBSERVADO' => 'warning',   // Amarillo
            'V' => 'success',           // Verde
            'A' => 'warning',           // Amarillo
            'N' => 'danger',            // Naranja (usando danger como aproximación)
            'R' => 'danger',            // Rojo
        ];
    }

    /**
     * Get overall status options
     */
    public static function getOverallStatusOptions(): array
    {
        return [
            'APTO' => 'APTO - Conforme General',
            'NO APTO' => 'NO APTO - No Conforme Crítico',
            'OBSERVADO' => 'OBSERVADO - Conforme con Observaciones',
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
        // Permitir archivos adjuntos en todos los items sin importar la prioridad
        return true;
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
            return 'APTO'; // Por defecto si no hay estados
        }
        
        // Prioridad de estados: NO APTO > OBSERVADO > APTO
        if (in_array('NO APTO', $allEstados, true) || in_array('R', $allEstados, true)) {
            return 'NO APTO';
        }
        if (in_array('OBSERVADO', $allEstados, true) || in_array('N', $allEstados, true) || in_array('A', $allEstados, true)) {
            return 'OBSERVADO';
        }
        return 'APTO';
    }

    /**
     * Get common Parte 2 items (same for all vessel types)
     */
    protected static function getCommonParte2(): array
    {
        return [
            ['item' => 'Livro de oleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Plano de segurança', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Plano de arranjo geral', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Plano de rede de carga e descarga', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Plano de caoacidade de tanques', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Teste de Opacidade', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Certificado de teste pneumático dos tanques de armazenamento de óleo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Certificado de Teste da rede de carga / descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Certificado de Teste da válvula de pressão e vácuo ', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
        ];
    }

    /**
     * Get common Parte 3 items (same for all vessel types)
     */
    protected static function getCommonParte3(): array
    {
        return [
            ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta nome e matrícula/número de registro pintados em ambos bordos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta nome e porto de matrícula pintados na popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os tanques estão identificados com sua capacidade de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam aberturas e escotilhas padronizadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As dimensões principais da embarcação (L, B, D) estão conforme seus certificados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta sobreposição em reparos estruturais?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os elementos estruturais apresentam continuidade estrutural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A sequência de soldagem nos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O processo de soldagem no chapeamento, elementos estruturais, conexões e acessórios está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O processo de soldagem apresenta defeitos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As conexões entre chapas e entre elementos estruturais estão alinhadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A instalação dos elementos estruturais está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As dimensões dos enxertos no chapeamento e nos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A estrutura da embarcação apresenta elementos concentradores de tensões?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A estrutura apresenta todos os seus elementos estruturais instalados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As dimensões dos elementos estruturais estão de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os tanques de carga são herméticos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os tanques de carga apresentam reparos de acordo com o padrão naval?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A barra redonda de aço, verduguete ou cinta de atrito (proteção do casco) encontra-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
        ];
    }

    /**
     * Get common Parte 4 items (same for all vessel types)
     */
    protected static function getCommonParte4(): array
    {
        return [
            ['item' => '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As bombas de descarga possuem sistema de parada de emergência remota claramente identificado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os motores das bombas estão operacionais, com proteções adequadas e escapes totalmente isolados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O sistema de escape do motor da bomba apresenta tela corta-chamas (antifaísca) e uma caixa inibidora de gases com seu visor de nível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A barreira de contenção possui tampões de embornais herméticos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O processo de soldagem da barreira de contenção está de acordo com o padrão naval?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O quadro de controle do sistema de alarme encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As baterias do sistema de alarme de nivel encontram-se em boas condições e estão guardadas em caixa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
        ];
    }

    /**
     * Get common Parte 5 items (same for all vessel types)
     */
    protected static function getCommonParte5(): array
    {
        return [
            ['item' => '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os equipamentos de FF e LSA estão devidamente pintados com o nome e matrícula ou nº de registro da embarcação, e contam com uma base adequada?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As máquinas e peças móveis a bordo estão protegidas com dispositivos de proteção eficazes para evitar acidentes?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O revestimento (pintura) do convés encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta Luz de Navegação Bombordo – Vermelha 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta Luz de Navegação Boreste – Verde 112.5° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Apresenta Luz de Topo – Branca 225° em boas condições?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com a norma?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A base das luzes de navegação encontra-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As escadas de acesso ao convés de carga encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
        ];
    }

    /**
     * Get common Parte 6 items (same for all vessel types)
     */
    protected static function getCommonParte6(): array
    {
        return [
            ['item' => '¿Conta-se com cabeços de amarração duplos ou simples, devidamente posicionados na proa e na popa?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Conta-se com cunhos ou cabeços e amarração simples adicionais, distribuídos em ambos bordos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços e cunhos estão em boas condições operacionais?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿A embarcação conta com olhais soldados ao casco, em ambos os bordos, para o trincamento das defensas durante as operações em terminais ou pontos de embarque?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿Os conectores das extremidades dos cabos e suas emendas de olho encontram-se em boas condições?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ['item' => '¿As emendas de olho cumprem o padrão das indústrias de segurança (número mínimo de grampos por diâmetro de cabo)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
        ];
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->overall_status = $model->calculateOverallStatus();
        });
    }
}
