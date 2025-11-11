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
                ['item' => 'Certificado nacional de arqueação', 'item_es' => 'Certificado de Arqueo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'item_es' => 'Certificado de Línea Máxima de Carga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'item_es' => 'Certificado de Matrícula', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'item_es' => 'Certificado Nacional de Seguridad para naves fluviales', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'item_es' => 'Certificado Nacional de Aprobación del Plan de Emergencia de a Bordo Contra la Contaminación del Medio Acuático por Hidrocarburos y/o Sustancias Nocivas Líquidas', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'item_es' => 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'item_es' => 'Certificado de Seguro de Responsabilidad Civil por Daños Causados por la Contaminación por Hidrocarburos', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'item_es' => 'Certificado de Aptitud para el Transporte Marítimo de Mercancías Peligrosas', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'item_es' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'item_es' => 'Certificado de Cumplimiento Relativo al Doble Casco', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'item_es' => 'Póliza de Casco Marítimo P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Declaração de conformidade para transporte de petróleo', 'item_es' => 'Ficha de Registro Medio de Transporte Fluvial (OSINERGMIN)', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Plano de segurança', 'item_es' => 'Plano de seguridad', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'item_es' => 'Plano de disposición general', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'item_es' => 'Certificados de Prueba Hidrostática y Mantenimiento de los Extintores', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Diagrama de rede de carga e descarga', 'item_es' => 'Plano del sistema de carga y descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de capacidade de tanques', 'item_es' => 'Plano de disposición de tanques', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de teste pneumático dos tanques de armazenamento de óleo', 'item_es' => 'Certificado de Prueba de Estanqueidad de los Tanques de Carga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da rede de carga / descarga', 'item_es' => 'Certificado de Prueba Hidrostática del Sistema de Carga y Descarga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de Teste da válvula de pressão e vácuo', 'item_es' => 'Certificado de Prueba de Válvulas de Presión y Vacío', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP', 'item_es' => 'Plan de emergencia a bordo para casos de derrame de hidrocarburos – Plan SOPEP', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Manual de contingência', 'item_es' => 'Plan de contingencia', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de carga, espaços vazios e cofferdams apresentam corrosão importante?', 'item_es' => '¿Los tanques de carga, tanque vacios y cofferdams presentan corrosión importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de registro pintados em ambos bordos?', 'item_es' => '¿Presenta nombre y puerto de registro rotulado en ambas bandas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula pintados na popa?', 'item_es' => '¿Presenta Nombre y puerto de matrícula rotulados en la popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?', 'item_es' => '¿Presenta marcas visibles del disco de Plimsoll en ambas bandas, de acuerdo con el certificado de línea máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?', 'item_es' => '¿Presenta marcas visibles de calado y escalas correspondientes en proa, popa y sección media de ambos costados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão identificados com sua capacidade de carga?', 'item_es' => '¿Los tanques están rotulados con su capacidad de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?', 'item_es' => '¿El casco, mamparos, planchajes y elementos estructurales estan libre de abolladuras/deformaciones significativas?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reforços estruturais apresentam continuidade estrutural?', 'item_es' => '¿Los refuerzos estructurales presentan continuidad estructural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A sequência de soldagem nos reforços estruturais apresenta resistência e estabilidade?', 'item_es' => '¿La secuencia de soldadura en los refuerzos estructurales presenta resistencia y estabilidad?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem no chapeamento, reforços estruturais e conexões apresenta continuidade e resistência?', 'item_es' => '¿La soldadura en el planchaje, refuerzos estructurales y conexiones presenta continuidad y resistencia?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As uniões soldadas apresentam fissuras, porosidade, escavação ou outros defeitos de execução?', 'item_es' => '¿Las uniones soldadas muestran fisuras, porosidad, socavación u otros defectos de ejecución?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As uniões entre chapa-chapa e reforço-reforço estão alinhadas?', 'item_es' => '¿Las uniones entre plancha-plancha y refuerzo-refuerzo estan alienadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?', 'item_es' => '¿El casco, mamparos, planchajes, elementos estructurales y conexiones presentan fracturas, roturas (agujeros)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos enxertos no chapamento e nos reforços estruturais garantem sua resistência e segurança?', 'item_es' => '¿Las dimensiones de los injertos en el planchaje y en los refuerzos estructurales garantizan su resistencia y seguridad?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?', 'item_es' => '¿Las tuberias de venteo, sondaje y las escotillas se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A estrutura da embarcação conta com todos os seus reforços estruturais completos e corretamente instalados?', 'item_es' => '¿La estructura de la nave cuenta con todos sus refuerzos estructurales completos y correctamente instalados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As dimensões dos reforços estruturais são adequadas para garantir a resistência e a segurança da embarcação?', 'item_es' => '¿Las dimensiones de los refuerzos estructurales son las adecuadas para garantizar resistencia y seguridad de la embarcación?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de carga são herméticos?', 'item_es' => '¿Los tanques de carga son estancos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reparos nos tanques de carga apresentam qualidade estrutural?', 'item_es' => '¿Las reparaciones de los tanques de carga presentan calidad estructural?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?', 'item_es' => '¿Los tanques, cofferdams, válvulas y respiraderos están debidamente rotulados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?', 'item_es' => '¿Cada tanque cuenta con venteo individual o por grupo de tanques, con respiraderos provistos de válvulas P/V o equivalentes?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?', 'item_es' => '¿Los vapores inflamables se descargan a ≥3 m de cualquier fuente de ignición?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bombas de descarga possuem sistema de parada de emergência remota claramente identificado?', 'item_es' => '¿Las bombas de descarga cuentan con sistema de parada de emergencia remota claramente identificado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O motor da bomba e a própria bomba estão em bom estado e operacionais?', 'item_es' => '¿El motor de la bomba y la propia bomba están en buen estado y operativos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta com braço de carga para as manobras de içamento e arriamento do mangote, e encontra-se em bom estado?', 'item_es' => '¿Cuenta con brazo de carga para las maniobras de izado y arriado de la manga, y se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'item_es' => '¿La capacidad de izaje del brazo de carga está marcada con su SWL (Safe Working Load) de forma visible?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?', 'item_es' => '¿Las tapas son herméticas a gases, con juntas compatibles y cierres seguros?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?', 'item_es' => '¿Todos los manifolds de carga y descarga, válvulas y conexiones están en buenas condiciones de funcionamiento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?', 'item_es' => '¿Las conexiones de tuberías y todos los extremos de tuberías no conectados estan totalmente fijados con pernos, ya sea a otra sección de tubería o a una brida ciega?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor da bomba apresenta tela corta-chamas (antifaísca) e uma caixa inibidora de gases com seu visor de nível?', 'item_es' => '¿El sistema de escape del motor de la bomba presenta malla cortallamas (antichispas) y una caja inhibidora de gases con su visor de nivel?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?', 'item_es' => '¿La línea de tuberías del sistema de carga-descarga cuenta únicamente con reducciones adecuadas para su uso?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?', 'item_es' => '¿Presenta una bandeja antiderrame adecuada bajo las lineas del colector de carga?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?', 'item_es' => '¿La bandeja antiderrame cuenta con una purga para drenaje que desemboca en el tanque de carga y cuenta con su respectivo dispositivo de cierre?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?', 'item_es' => '¿La soldadura de la bandeja antiderrame presenta continuidad y acabados adecuados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?', 'item_es' => '¿Las tuberias de venteo, tapas de registro y las tuberias del sistema de carga-descarga estan totalmente fijados con pernos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A barreira de contenção possui tampões de embornais herméticos?', 'item_es' => '¿La barrera de contención cuenta con tapones de imbornal herméticos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem da barreira de contenção do convés apresenta continuidade, integridade e acabamento adequados?', 'item_es' => '¿La soldadura de la barrera de contención de cubierta presenta continuidad, integridad y acabado adecuados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?', 'item_es' => '¿Cada tanque cuenta con alarma visual y acústica de alto nivel?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?', 'item_es' => '¿Las embarcaciones con bombas de carga cuentan con manómetros en el colector y en la salida?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O quadro de controle do sistema de alarme encontra-se em bom estado?', 'item_es' => '¿El Tablero de control del sistema de alarma se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?', 'item_es' => '¿La acometida del cableado electrico, conexiones, conectores se encuentran en buen estado, están correctamente aislados, tienen hermeticidad y proporcionan una conexión segura y fiable?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias encontram-se em bom estado, instaladas em caixa de segurança e com terminais devidamente protegidos?', 'item_es' => '¿Las baterías se encuentran en buen estado, instaladas en caja de seguridad y con bornes debidamente protegidos?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?', 'item_es' => '¿La cubierta dispone de plataformas de acceso sobre las tuberías y pintura antideslizante en todo el trayecto y señalización visible?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente identificados com o nome e porto de registro, e possuem uma base adequada para sua estiva?', 'item_es' => '¿Los equipos de FF y LSA están debidamente identificados con el nombre y puerto de registro, y cuentan con una base adecuada para su estiba?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'item_es' => '¿Los equipos de FF y LSA se encuentran en buen estado y/u operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?', 'item_es' => '¿Los aros salvavidas presentan rabiza, guirnalda y cinta reflectiva adecuada?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?', 'item_es' => '¿La embarcación cuenta con al menos dos aros salvavidas (ubicados a proa y popa) y dos extintores portátiles disponible durante todas las operaciones (Independientes del equipo permanente de los buques de asistencia), y respetando la cantidad establecida en su certificado estatutario y/o plano de seguridad?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?', 'item_es' => '¿Existen en cubierta señales visibles que indiquen: areas de fumadores/no fumadores, Prohibición de luz al descubierto, Acceso restringido a personal no autorizado, Carga peligrosa, Restricción de uso de dispositivos no intrínsecamente seguros, señales de protección ambiental (no arrojar residuos al agua)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?', 'item_es' => '¿La embarcación cuenta con Kit de Respuesta a Derrames, debidamente identificado y con capacidad suficiente para controlar fugas menores?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As máquinas e peças móveis a bordo estão providas de proteções ou coberturas de segurança?', 'item_es' => '¿Las máquinas y piezas móviles a bordo están provistas de resguardos o cubiertas de seguridad?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, convés e/ou praça de bombas estão livres de manchas de óleo/hidrocarbonetos?', 'item_es' => '¿El casco, cubierta y/o sala de bombas estan libre de manchas de aceite/hidrocarburos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) do convés encontra-se em bom estado?', 'item_es' => '¿El recubrimiento (Pintura) de la cubierta está en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?', 'item_es' => '¿Las zonas de tránsito están claramente señalizadas con pintura antideslizante?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Bombordo – Vermelha em boas condições?', 'item_es' => '¿Presenta Luz de Situación Babor - Rojo en buenas condiciones?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Navegação Boreste – Verde em boas condições?', 'item_es' => '¿Presenta Luz de Situación Estribor - Verde en buenas condiciones?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta Luz de Topo – Branca em boas condições?', 'item_es' => '¿Presenta Luz de Tope - Blanca en buenas condiciones?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de escape do motor está corretamente isolado em toda a sua extensão?', 'item_es' => '¿El sistema de escape del motor está correctamente aislado en toda su extensión?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?', 'item_es' => '¿El arco de visibilidad de las luces de navegación esta acorde con el RIPA?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A base das luzes de navegação encontra-se em bom estado?', 'item_es' => '¿La base de las luces de navegación se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As escadas de acesso ao convés de carga encontram-se em bom estado?', 'item_es' => '¿Las escaleras de acceso a la cubierta de carga se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?', 'item_es' => '¿Las barandas en cubierta de carga, principal o techo de sala de maquinas se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_6' => [
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?', 'item_es' => '¿Los winches, cabestrantes, rodillos, bitas y cornamusas están en buenas condiciones operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'item_es' => '¿Cada dispositivo o elemento de amarre está identificado con su SWL (Safe Working Load) de forma visible?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'item_es' => '¿Los elementos de amarre entre barcaza - barcaza y barcaza - empujador, se encuentran en buen estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação dispõe em ambas as bandas de defensas em bom estado?', 'item_es' => '¿La embarcación dispone en ambas bandas de defensas en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?', 'item_es' => '¿El empalme de ojo del cable de amarre se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
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
                ['item' => 'Certificado nacional de arqueação', 'item_es' => 'Certificado de Arqueo', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado nacional de borda livre para a navegação interior', 'item_es' => 'Certificado de Línea Máxima de Carga', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)', 'item_es' => 'Certificado de Matrícula', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de segurança de navegação', 'item_es' => 'Certificado Nacional de Seguridad para naves fluviales', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de operação - IPAAM', 'item_es' => 'Certificado Nacional de Aprobación del Plan de Emergencia de a Bordo Contra la Contaminación del Medio Acuático por Hidrocarburos y/o Sustancias Nocivas Líquidas', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANP', 'item_es' => 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização de ANTAQ', 'item_es' => 'Certificado de Seguro de Responsabilidad Civil por Daños Causados por la Contaminación por Hidrocarburos', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA', 'item_es' => 'Certificado de Aptitud para el Transporte Marítimo de Mercancías Peligrosas', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de regularidade - IBAMA', 'item_es' => 'Certificado de regularidade - IBAMA', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificado de registro de armador (CRA)', 'item_es' => 'Certificado de Cumplimiento Relativo al Doble Casco', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Apolice de seguro P&I', 'item_es' => 'Póliza de Casco Marítimo P&I', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Cartão de tripulação de segurança (CTS)', 'item_es' => 'Certificado de Dotación Mínima', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Licença de estação de navio', 'item_es' => 'Permiso para Operar una Estación de Comunicación de Teleservicio Móvil', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_2' => [
                ['item' => 'Plano de segurança', 'item_es' => 'Plano de seguridad', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de arranjo geral', 'item_es' => 'Plano de disposición general', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio', 'item_es' => 'Certificados de Prueba Hidrostática y Mantenimiento de los Extintores', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],

                ['item' => 'Certificado de controle de Praga', 'item_es' => 'Certificado de Fumigación, Desinfección y Desratización', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Plano de incêndio', 'item_es' => 'Plano contraincendio', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Pessoa Responsável designada', 'item_es' => 'Persona responsable designada', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => 'Crew List de saida', 'item_es' => 'Crew List de salida', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_3' => [
                ['item' => '¿Os tanques de carga, espaços vazios e cofferdams apresentam corrosão significativa?', 'item_es' => '¿Los tanques de combustible, tanques vacíos y cofferdam presentan corrosión importante?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de registro rotulados em ambas as bandas?', 'item_es' => '¿Presenta nombre y puerto de registro rotulado en ambas bandas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta nome e porto de matrícula rotulados na popa?', 'item_es' => '¿Presenta Nombre y puerto de matrícula rotulados en la popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas visíveis do disco de Plimsoll em ambas as bandas, de acordo com o certificado de linha máxima de carga?', 'item_es' => '¿Presenta marcas visibles del disco de Plimsoll en ambas bandas, de acuerdo con el certificado de línea máxima de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta marcas visíveis de calado e escalas correspondentes na proa, popa e seção média de ambos os costados?', 'item_es' => '¿Presenta marcas visibles de calado y escalas correspondientes en proa y popa?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques estão rotulados com sua capacidade de carga?', 'item_es' => '¿Los tanques están rotulados con su capacidad de carga?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, planchadas e elementos estruturais estão livres de amassados/deformações significativas?', 'item_es' => '¿El casco, mamparos, planchajes y elementos estructurales estan libre de abolladuras/deformaciones significativas?', 'prioridad' => 2, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os reforços estruturais apresentam continuidade estrutural?', 'item_es' => '¿Los refuerzos estructurales presentan continuidad estructural?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A soldagem do casco e da estrutura encontra-se em boas condições?', 'item_es' => '¿La soldadura del casco y estructura se encuentran en buenas condiciones?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O casco, anteparas, planchadas, elementos estruturais e conexões apresentam fissuras, rupturas (furos)?', 'item_es' => '¿El casco, mamparos, planchajes, elementos estructurales y conexiones presentan fracturas, roturas (agujeros)?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As tubulações de ventilação, sondagem e escotilhas estão em bom estado?', 'item_es' => '¿Las tuberias de venteo, sondaje y las escotillas se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As defensas encontram-se em bom estado?', 'item_es' => '¿Las defensas se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os motores de propulsão possuem controles no passadiço e na praça de máquinas?', 'item_es' => '¿Los motores de propulsión cuentan con mandos desde el puente y sala de máquinas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os motores de propulsão estão em bom estado e operacionais?', 'item_es' => '¿Los motores de propulsión están en buen estado y operativos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O botão de parada de emergência, localizado no passadiço e na praça de máquinas, encontra-se operacional?', 'item_es' => '¿El botón de parada de emergencia en el puente y sala de máquinas se encuentra operativo?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com sistema de governo principal e auxiliar em condições operacionais?', 'item_es' => '¿La embarcación cuenta con sistema de gobierno principal y auxiliar en condiciones operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui na ponte de comando um indicador de ângulo de pala (axiómetro) em bom estado de funcionamento?', 'item_es' => '¿Cuenta en puente de mando con un indicador de ángulo de pala (axiómetro) en buen estado de funcionamiento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O tanque de óleo hidráulico está em bom estado e possui visor de nível adequado e tubulação de ventilação?', 'item_es' => '¿El tanque de aceite hidraulico está en buen estado y cuenta con un visor de nivel adecuado y tuberia de venteo?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As prensa-estopas dos sistemas de governo e propulsão estão corretamente instaladas e garantem estanqueidade?', 'item_es' => '¿Las prensaestopas de los sistemas de gobierno y propulsión están correctamente instaladas y garantizan hermeticidad?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de combustível possuem visor de nível adequado, tubulação de ventilação com proteção contra chamas (arresta-chamas) e tubulação de enchimento com bandeja anti-derrame?', 'item_es' => '¿Los tanques de combustible cuentan visor de nivel adecuada, tuberia de venteo con proteccion contra llamas (arrestaflama) y con tuberia de llenado con bandeja antiderrame?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os tanques de combustível, juntamente com suas tubulações de ventilação, enchimento e tampas de registro, estão rotulados?', 'item_es' => '¿Los tanques de combustible junto con sus tuberias de venteo, llenado y sus tapas de registro están rotulados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A embarcação conta com grupos geradores principal e auxiliar em condições operacionais?', 'item_es' => '¿La embarcación cuenta con grupos electrógenos principal y auxiliar en condiciones operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os quadros elétricos e a rede de alimentação encontram-se em bom estado?', 'item_es' => '¿Los tableros electricos y la acometida se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistemas de água doce, serviços gerais e dejeto em bom estado de funcionamento?', 'item_es' => '¿Presenta sistemas de agua dulce, servicios generales y aguas negras en buen estado de funcionamiento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O tanque de água doce e os serviços gerais estão em bom estado?', 'item_es' => '¿El tanque de agua dulce y servicios generales están en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistemas de esgoto dos compartimentos vazios e porão da sala de máquinas em bom estado de funcionamento?', 'item_es' => '¿Presenta sistemas de achique de los compartimientos vacios y sentina de la sala de maquinas en buen estado de funcionamiento?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta alarme de porão (sonoro) de nível alto na sala de máquinas em condições operacionais?', 'item_es' => '¿Presenta alarma de sentina (sonora) de nivel alto en la sala de maquinas en condiciones operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Conta com tanques de retenção do sistema de sentina e do sistema de águas negras?', 'item_es' => '¿Cuenta con tanques de retención del sistema de sentina y del sistema de aguas negras?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_4' => [
                ['item' => '¿Possui certificado de teste de opacidade atualizado?', 'item_es' => '¿Cuenta con certificado de Test de opacidad actualizado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui diário de navegação?', 'item_es' => '¿Cuenta con diario de navegación?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui diário de máquinas?', 'item_es' => '¿Cuenta con diario de máquinas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Ao menos um tripulante deve estar familiarizado com os equipamentos de rádio?', 'item_es' => '¿Al menos un tripulante debe estar familiarizado con los equipos de radio?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A iluminação interna e externa do navio é adequada e encontra-se em bom estado de funcionamento?', 'item_es' => '¿La iluminación interna y externa de la nave es adecuada y se encuentra en buen estado de funcionamiento?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta sistema de iluminação de emergência na sala de máquinas?', 'item_es' => '¿Presenta sistema de iluminación de emergencia en la sala de máquinas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As luzes de navegação estão operacionais?', 'item_es' => '¿Las luces de navegación están operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?', 'item_es' => '¿El arco de visibilidad de las luces de navegación esta acorde con el RIPA?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Apresenta os seguintes equipamentos de navegação e comunicação em bom estado?: * Um (01) Faro Pirata * Um (01) GPS * Uma (01) ecosonda * Um (01) radar * Uma (01) Rádio VHF', 'item_es' => 'Presenta los siguientes equipos de navegación y comunicación en buen estado?: - Un (01) Faro pirata - Un (01) GPS - Una (01) Ecosonda - Un (01) Radar - Una (01) Radio VHF', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As baterias dos equipamentos de comunicação e navegação encontram-se em bom estado?', 'item_es' => '¿Las baterías de los equipos de comunicación y navegación se encuentran en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA estão devidamente rotulados?', 'item_es' => '¿Los equipos de FF y LSA están debidamente rotulados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?', 'item_es' => '¿Los equipos de FF y LSA se encuentran en buen estado y/u operativos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os coletes salva-vidas possuem corda, guirlanda e fita refletiva adequadas?', 'item_es' => '¿Los aros salvavidas cuentan con cuerda, guirnalda y cinta reflectiva adecuadas?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os coletes salva-vidas possuem luz, apito, faixas refletivas e estão marcados com o nome do barco e seu porto de registro?', 'item_es' => '¿Los chalecos salvavidas cuentan con luz, silbato, bandas reflectivas y están marcados con el nombre de la embarcación y su puerto de registro?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A quantidade de anéis salva-vidas e extintores portáteis está de acordo com o plano de segurança?', 'item_es' => '¿La cantidad de aros salvavidas y extintores portátiles está de acuerdo con el plano de seguridad?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui sinalização de advertência visível em áreas de risco: "Perigo Combustível" e "Proibido Fumar"?', 'item_es' => '¿Cuenta con señalización de advertencia visible en áreas de riesgo: "Peligro Combustible" y "Prohibido Fumar"?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os produtos inflamáveis encontram-se em áreas seguras?', 'item_es' => '¿Los productos inflamables se encuentran en áreas seguras?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os artefatos de gás e tubulações estão corretamente estibados?', 'item_es' => '¿Los artefactos de gas y tuberías están correctamente estibados?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui botão geral de alarme de emergência (sonoro) em bom estado de funcionamento e pelo menos um no passadiço?', 'item_es' => '¿Presenta botón general de alarma de emergencia (sonora) en buen estado de funcionamiento y al menos uno está en el puente?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O convés e a sala de máquinas estão corretamente iluminadas, ventiladas e livres de obstáculos (limpas e organizadas)?', 'item_es' => '¿La cubierta y la sala de máquinas están correctamente iluminadas, ventiladas y libres de obstáculos (limpias y organizadas)?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O empurrador possui embarcação auxiliar?', 'item_es' => '¿La nave cuenta con una embarcación auxiliar?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Possui kit de primeiros socorros?', 'item_es' => '¿Cuenta con kit de primeros auxilios?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O sistema de combate a incêndio possui bomba exclusiva, manômetro, hidrantes, mangueiras e acoplamentos de abertura rápida em bom estado e estão operacionais?', 'item_es' => '¿El sistema de lucha contra incendios cuenta con bomba exclusiva, manómetro, hidrantes, mangueras y acoplamientos de apertura rápida en buen estado y están operativos?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O revestimento (pintura) da coberta e superestrutura está em bom estado?', 'item_es' => '¿El recubrimiento (pintura) de la cubierta y superestructura está en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
            ],
            'parte_5' => [
                ['item' => '¿O braço de carga com guincho (manual e/ou elétrico) encontra-se operacional e em condições seguras de uso?', 'item_es' => '¿El brazo de carga con winche (manual y/o eléctrico) se encuentra operativo y en condiciones seguras de uso?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?', 'item_es' => '¿La capacidad de izaje del brazo de carga está marcada con su SWL (Safe Working Load) de forma visible?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿As bitas duplas, simples e/ou cornamusas estão instaladas em número suficiente e corretamente distribuídas ao longo da embarcação?', 'item_es' => '¿Las bitas dobles, simples y/o cornamusas están instaladas en número suficiente y correctamente distribuidas a lo largo de la embarcación?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?', 'item_es' => '¿Los winches, cabestrantes, rodillos, bitas y cornamusas están en buenas condiciones operativas?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?', 'item_es' => '¿Cada dispositivo o elemento de amarre está identificado con su SWL (Safe Working Load) de forma visible?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?', 'item_es' => '¿Los elementos de amarre entre barcaza - barcaza y barcaza - empujador se encuentran en buen estado?', 'prioridad' => 1, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿Dispõe de defensas adequadas para as manobras de empuxo e atracação?', 'item_es' => '¿Dispone de defensas adecuadas para las maniobras de empuje y atraque?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
                ['item' => '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?', 'item_es' => '¿El empalme de ojo del cable de amarre se encuentra en buen estado?', 'prioridad' => 3, 'checkbox_1' => false, 'checkbox_2' => false, 'estado' => '', 'comentarios' => '', 'archivos_adjuntos' => []],
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
     *
     * Lógica de evaluación según LOGICA_EVALUACION_CONCEPTUAL_IA.md:
     * - Si existe al menos un ítem con estado 'N' (NO APTO) → Resultado: NO APTO
     * - Si no hay 'N' pero existe al menos un ítem con estado 'O' (OBSERVADO) → Resultado: OBSERVADO
     * - Si todos los ítems tienen estado 'A' (APTO) → Resultado: APTO
     *
     * Valores de estado:
     * - 'A' = APTO (Cumple con los requisitos)
     * - 'N' = NO APTO (No cumple - Prioridad 1 Crítica)
     * - 'O' = OBSERVADO (No cumple - Prioridad 2-3 No crítica)
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

        // Si no hay estados evaluados, retornar APTO por defecto
        if (empty($allEstados)) {
            return 'APTO';
        }

        // Paso 1: Verificar si existe algún ítem NO APTO (crítico)
        // Principio de severidad máxima: un solo incumplimiento crítico rechaza toda la inspección
        if (in_array('N', $allEstados, true)) {
            return 'NO APTO';
        }

        // Paso 2: Si no hay NO APTO, verificar si existe algún ítem OBSERVADO (no crítico)
        if (in_array('O', $allEstados, true)) {
            return 'OBSERVADO';
        }

        // Paso 3: Si no hay NO APTO ni OBSERVADO, todos los ítems están APTO
        return 'APTO';
    }

    /**
     * Verificar si el checklist tiene estructura válida (con 'estado' en items)
     * Los checklists antiguos no tienen esta estructura
     */
    public function isValidChecklistStructure(): bool
    {
        for ($i = 1; $i <= 6; $i++) {
            $items = $this->getAttribute('parte_' . $i . '_items') ?? [];

            if (!empty($items)) {
                foreach ($items as $item) {
                    // Si algún item no tiene la clave 'estado', es estructura antigua
                    if (!isset($item['estado']) && !array_key_exists('estado', $item)) {
                        return false;
                    }
                }
            }
        }

        return true;
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
