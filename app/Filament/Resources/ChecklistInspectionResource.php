<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChecklistInspectionResource\Pages;
use App\Models\ChecklistInspection;
use App\Models\Vessel;
use App\Models\Owner;
use App\Models\User;
use App\Models\VesselDocument;
use App\Models\VesselDocumentType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;

class ChecklistInspectionResource extends Resource
{
    protected static ?string $model = ChecklistInspection::class;

    protected static ?string $navigationIcon = 'heroicon-o-check-circle';

    protected static ?string $navigationGroup = 'Inspecciones';

    protected static ?string $navigationLabel = 'Inspecciones Checklist';

    protected static ?string $modelLabel = 'Inspección Checklist';

    protected static ?string $pluralModelLabel = 'Inspecciones Checklist';

    protected static ?int $navigationSort = 5;

    /**
     * Mapeo entre los tipos de documentos de vessel_documents y los ítems del checklist
     */
    protected static function getDocumentItemMapping(): array
    {
        return [
            // PARTE 1 - DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO
            VesselDocumentType::CERTIFICADO_ARQUEACAO => 'Certificado nacional de arqueação',
            VesselDocumentType::CERTIFICADO_BORDA_LIVRE => 'Certificado nacional de borda livre para a navegação interior',
            VesselDocumentType::PROVISAO_REGISTRO => 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)',
            VesselDocumentType::DECLARACAO_CONFORMIDADE => 'Declaração de conformidade para transporte de petróleo',
            VesselDocumentType::CERTIFICADO_SEGURANCA => 'Certificado de segurança de navegação',
            VesselDocumentType::LICENCA_IPAAM => 'Licença de operação - IPAAM',
            VesselDocumentType::AUTORIZACAO_ANP => 'Autorização de ANP',
            VesselDocumentType::AUTORIZACAO_ANTAQ => 'Autorização de ANTAQ',
            VesselDocumentType::AUTORIZACAO_IBAMA => 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA',
            VesselDocumentType::CERTIFICADO_REGULARIDADE => 'Certificado de regularidade - IBAMA',
            VesselDocumentType::CERTIFICADO_ARMADOR => 'Certificado de registro de armador (CRA)',
            VesselDocumentType::APOLICE_SEGURO => 'Apolice de seguro P&I',
            
            // PARTE 2 - DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO
            VesselDocumentType::PLANO_SEGURANCA => 'Plano de segurança',
            VesselDocumentType::PLANO_ARRANJO => 'Plano de arranjo geral',
            VesselDocumentType::PLANO_REDE_CARGA => 'Plano de rede de carga e descarga',
            VesselDocumentType::PLANO_CAPACIDADE => 'Plano de caoacidade de tanques',
            VesselDocumentType::CERTIFICADO_PNEUMATICO => 'Certificado de teste pneumático dos tanques de armazenamento de óleo',
            VesselDocumentType::CERTIFICADO_REDE => 'Certificado de Teste da rede de carga / descarga',
            VesselDocumentType::CERTIFICADO_VALVULA => 'Certificado de Teste da válvula de pressão e vácuo ',
            VesselDocumentType::PLANO_SOPEP => 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP',
            VesselDocumentType::CERTIFICADO_EXTINTORES => 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio',
            
            // DOCUMENTOS EXCLUSIVOS PARA BARCAZAS
            VesselDocumentType::DECLARACAO_CONFORMIDADE => 'Declaração de conformidade para transporte de petróleo',
            
            // DOCUMENTOS EXCLUSIVOS PARA EMPUJADORES
            VesselDocumentType::CARTAO_TRIPULACAO => 'Cartão de tripulação de segurança (CTS)',
            VesselDocumentType::LICENCA_ESTACAO => 'Licença de estação de navio',
            VesselDocumentType::CERTIFICADO_CONTROLE => 'Certificado de controle de Praga',
            VesselDocumentType::PLANO_INCENDIO => 'Plano de incêndio',
            VesselDocumentType::OPERADOR_TECNICO => 'Pessoa Responsável designada',
            VesselDocumentType::CREW_LIST => 'Crew List de saida',

            // DOCUMENTOS EXCLUSIVOS PARA MOTOCHATAS
            VesselDocumentType::MOTOCHATA_DOCUMENTO_1 => 'Documento especial motochata 1',
            VesselDocumentType::MOTOCHATA_DOCUMENTO_2 => 'Documento especial motochata 2',
        ];
    }

    /**
     * Traducción cosmética de textos en portugués a formato bilingüe para visualización
     * NO modifica los datos almacenados, solo agrega la traducción al español para mostrar
     *
     * @param string $itemPT - Texto en portugués
     * @param string|null $itemES - Texto en español (opcional, si existe en DB)
     * @return string - HTML con banderas y texto bilingüe
     */
    protected static function translateItemForDisplay(string $itemPT, ?string $itemES = null): string
    {
        // Imágenes de banderas desde CDN (flag-icons)
        $flagBR = '<img src="https://flagcdn.com/16x12/br.png" srcset="https://flagcdn.com/32x24/br.png 2x, https://flagcdn.com/48x36/br.png 3x" width="16" height="12" alt="Brasil" style="margin-right:4px;vertical-align:middle;display:inline-block;">';
        $flagPE = '<img src="https://flagcdn.com/16x12/pe.png" srcset="https://flagcdn.com/32x24/pe.png 2x, https://flagcdn.com/48x36/pe.png 3x" width="16" height="12" alt="Perú" style="margin-right:4px;vertical-align:middle;display:inline-block;">';

        // Si ya tenemos la traducción en español (desde la DB), usarla directamente
        if (!empty($itemES)) {
            return $flagBR . $itemPT . '&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . $itemES;
        }

        // Si no, buscar en el diccionario de traducciones (para compatibilidad con registros antiguos)
        $translations = [
            // PARTE 1 - DOCUMENTOS DE BANDEIRA E APOLICES DE SEGURO (BARCAZA)
            'Certificado nacional de arqueação' => $flagBR . 'Certificado nacional de arqueação&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Arqueo',
            'Certificado nacional de borda livre para a navegação interior' => $flagBR . 'Certificado nacional de borda livre para a navegação interior&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Línea Máxima de Carga',
            'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)' => $flagBR . 'Provisão de registro da propriedade marítima (ou Documento provisório de propiedade)&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Matrícula',
            'Declaração de conformidade para transporte de petróleo' => $flagBR . 'Declaração de conformidade para transporte de petróleo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Ficha de Registro Medio de Transporte Fluvial (OSINERGMIN)',
            'Certificado de segurança de navegação' => $flagBR . 'Certificado de segurança de navegação&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado Nacional de Seguridad para naves fluviales',
            'Licença de operação - IPAAM' => $flagBR . 'Licença de operação - IPAAM&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado Nacional de Aprobación del Plan de Emergencia de a Bordo Contra la Contaminación del Medio Acuático por Hidrocarburos y/o Sustancias Nocivas Líquidas',
            'Autorização de ANP' => $flagBR . 'Autorização de ANP&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Permiso de Operaciones para Prestar Servicio de Transporte Fluvial',
            'Autorização de ANTAQ' => $flagBR . 'Autorização de ANTAQ&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Seguro de Responsabilidad Civil por Daños Causados por la Contaminación por Hidrocarburos',
            'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA' => $flagBR . 'Autorização ambiental Para o transporte interestadual de produtos perigosos - IBAMA&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Aptitud para el Transporte Marítimo de Mercancías Peligrosas',
            'Certificado de regularidade - IBAMA' => $flagBR . 'Certificado de regularidade - IBAMA',
            'Certificado de registro de armador (CRA)' => $flagBR . 'Certificado de registro de armador (CRA)&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Cumplimiento Relativo al Doble Casco',
            'Apolice de seguro P&I' => $flagBR . 'Apólice de seguro P&I&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Póliza de Casco Marítimo P&I',

            // PARTE 2 - DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO (BARCAZA)
            'Plano de segurança' => $flagBR . 'Plano de segurança&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de seguridad',
            'Plano de arranjo geral' => $flagBR . 'Plano de arranjo geral&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de disposición general',
            'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio' => $flagBR . 'Certificados de Teste Hidrostático e Manutenção para Extintores de Incêndio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificados de Prueba Hidrostática y Mantenimiento de los Extintores',
            'Plano de rede de carga e descarga' => $flagBR . 'Plano de rede de carga e descarga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano del sistema de carga y descarga',
            'Plano de caoacidade de tanques' => $flagBR . 'Plano de capacidade de tanques&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano de disposición de tanques',
            'Certificado de teste pneumático dos tanques de armazenamento de óleo' => $flagBR . 'Certificado de teste pneumático dos tanques de armazenamento de óleo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Estanqueidad de los Tanques de Carga',
            'Certificado de Teste da rede de carga / descarga' => $flagBR . 'Certificado de Teste da rede de carga / descarga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba Hidrostática del Sistema de Carga y Descarga',
            'Certificado de Teste da válvula de pressão e vácuo' => $flagBR . 'Certificado de Teste da válvula de pressão e vácuo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Válvulas de Presión y Vacío',
            'Certificado de Teste da válvula de pressão e vácuo ' => $flagBR . 'Certificado de Teste da válvula de pressão e vácuo&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Prueba de Válvulas de Presión y Vacío',
            'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP' => $flagBR . 'Plano de Emergência a Bordo para Poluição por Óleo - SOPEP&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plan de emergencia a bordo para casos de derrame de hidrocarburos – Plan SOPEP',
            'Plano de contingência' => $flagBR . 'Plano de contingência&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plan de contingencia',

            // PARTE 3 - CASCO Y ESTRUTURAS (BARCAZA)
            '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?' => $flagBR . '¿Os tanques de carga, espaços vazios e/ou compartimentos apresentam corrosão importante?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques de carga, tanque vacios y/o compartimentos presentan corrosión importante?',
            '¿Apresenta nome e porto de registro pintados em ambos bordos?' => $flagBR . '¿Apresenta nome e porto de registro pintados em ambos bordos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta nombre y puerto de registro rotulado en ambas bandas?',
            '¿Apresenta nome e porto de matrícula pintados na popa?' => $flagBR . '¿Apresenta nome e porto de matrícula pintados na popa?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta Nombre y puerto de matrícula rotulados en la popa?',
            '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?' => $flagBR . '¿Apresenta marcas do disco de Plimsoll em ambos bordos, de acordo com o certificado de linha máxima de carga?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta marcas visibles del disco de Plimsoll en ambas bandas, de acuerdo con el certificado de línea máxima de carga?',
            '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?' => $flagBR . '¿Apresenta marcas de calado e respectivas escalas na proa, popa e seção média de ambos costados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta marcas visibles de calado y escalas correspondientes en proa, popa y sección media de ambos costados?',
            '¿Os tanques estão identificados com sua capacidade de carga?' => $flagBR . '¿Os tanques estão identificados com sua capacidade de carga?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques están rotulados con su capacidad de carga?',
            '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?' => $flagBR . '¿O casco, anteparas, chapas e elementos estruturais estão livres de amassados/deformações significativas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El casco, mamparos, planchajes y elementos estructurales estan libre de abolladuras/deformaciones significativas?',
            '¿Os reforços estruturais apresentam continuidade estrutural?' => $flagBR . '¿Os reforços estruturais apresentam continuidade estrutural?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los refuerzos estructurales presentan continuidad estructural?',
            '¿A sequência de soldagem nos reforços estruturais apresenta resistência e estabilidade?' => $flagBR . '¿A sequência de soldagem nos reforços estruturais apresenta resistência e estabilidade?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La secuencia de soldadura en los refuerzos estructurales presenta resistencia y estabilidad?',
            '¿A soldagem no chapeamento, reforços estruturais e conexões apresenta continuidade e resistência?' => $flagBR . '¿A soldagem no chapeamento, reforços estruturais e conexões apresenta continuidade e resistência?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La soldadura en el planchaje, refuerzos estructurales y conexiones presenta continuidad y resistencia?',
            '¿As uniões soldadas apresentam fissuras, porosidade, escavação ou outros defeitos de execução?' => $flagBR . '¿As uniões soldadas apresentam fissuras, porosidade, escavação ou outros defeitos de execução?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las uniones soldadas muestran fisuras, porosidad, socavación u otros defectos de ejecución?',
            '¿As uniões entre chapa-chapa e reforço-reforço estão alinhadas?' => $flagBR . '¿As uniões entre chapa-chapa e reforço-reforço estão alinhadas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las uniones entre plancha-plancha y refuerzo-refuerzo estan alienadas?',
            '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?' => $flagBR . '¿O casco, anteparas, chapas, elementos estruturais e conexões apresentam fraturas, quebras (furos)?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El casco, mamparos, planchajes, elementos estructurales y conexiones presentan fracturas, roturas (agujeros)?',
            '¿As dimensões dos enxertos no chapamento e nos reforços estruturais garantem sua resistência e segurança?' => $flagBR . '¿As dimensões dos enxertos no chapamento e nos reforços estruturais garantem sua resistência e segurança?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las dimensiones de los injertos en el planchaje y en los refuerzos estructurales garantizan su resistencia y seguridad?',
            '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?' => $flagBR . '¿As tubulações de respiro, sondagem e as escotilhas encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las tuberias de venteo, sondaje y las escotillas se encuentran en buen estado?',
            '¿A estrutura da embarcação conta com todos os seus reforços estruturais completos e corretamente instalados?' => $flagBR . '¿A estrutura da embarcação conta com todos os seus reforços estruturais completos e corretamente instalados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La estructura de la nave cuenta con todos sus refuerzos estructurales completos y correctamente instalados?',
            '¿As dimensões dos reforços estruturais são adequadas para garantir a resistência e a segurança da embarcação?' => $flagBR . '¿As dimensões dos reforços estruturais são adequadas para garantir a resistência e a segurança da embarcação?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las dimensiones de los refuerzos estructurales son las adecuadas para garantizar resistencia y seguridad de la embarcación?',
            '¿Os tanques de carga são herméticos?' => $flagBR . '¿Os tanques de carga são herméticos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques de carga son estancos?',
            '¿Os reparos nos tanques de carga apresentam qualidade estrutural?' => $flagBR . '¿Os reparos nos tanques de carga apresentam qualidade estrutural?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las reparaciones de los tanques de carga presentan calidad estructural?',

            // PARTE 4 - SISTEMAS DE CARGA E DESCARGA (BARCAZA)
            '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?' => $flagBR . '¿Os tanques, cofferdams, válvulas e respiros estão devidamente pintados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques, cofferdams, válvulas y respiraderos están debidamente rotulados?',
            '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?' => $flagBR . '¿Cada tanque possui respiro individual ou por grupo de tanques, com respiros providos de válvulas P/V ou equivalentes?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cada tanque cuenta con venteo individual o por grupo de tanques, con respiraderos provistos de válvulas P/V o equivalentes?',
            '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?' => $flagBR . '¿Os vapores inflamáveis são descarregados a ≥3 m de qualquer fonte de ignição?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los vapores inflamables se descargan a ≥3 m de cualquier fuente de ignición?',
            '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?' => $flagBR . '¿As tampas são herméticas a gases, com juntas compatíveis e fechos seguros?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las tapas son herméticas a gases, con juntas compatibles y cierres seguros?',
            '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?' => $flagBR . '¿Todos os manifolds (coletor) de carga e descarga, válvulas, conexões e/ou equipamentos associados às bombas estão em boas condições de funcionamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Todos los manifolds de carga y descarga, válvulas y conexiones están en buenas condiciones de funcionamiento?',
            '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?' => $flagBR . '¿As conexões das tubulações e todas as extremidades de tubulações não conectadas estão totalmente fixadas com parafusos, seja a outra seção de tubulação ou a uma flange cega?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las conexiones de tuberías y todos los extremos de tuberías no conectados estan totalmente fijados con pernos, ya sea a otra sección de tubería o a una brida ciega?',
            '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?' => $flagBR . '¿A linha de tubulações do sistema de carga-descarga possui reduções de acordo com o padrão naval?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La línea de tuberías del sistema de carga-descarga cuenta únicamente con reducciones adecuadas para su uso?',
            '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?' => $flagBR . '¿Apresenta uma bandeja de contenção de derrame adequada sob as linhas do coletor de carga?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta una bandeja antiderrame adecuada bajo las lineas del colector de carga?',
            '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?' => $flagBR . '¿A bandeja de contenção possui uma purga para drenagem que desemboca no tanque de carga e conta com seu respectivo dispositivo de fechamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La bandeja antiderrame cuenta con una purga para drenaje que desemboca en el tanque de carga y cuenta con su respectivo dispositivo de cierre?',
            '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?' => $flagBR . '¿O processo de soldagem da bandeja de contenção está de acordo com o padrão naval?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La soldadura de la bandeja antiderrame presenta continuidad y acabados adecuados?',
            '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?' => $flagBR . '¿As tubulações de respiro, tampas de inspeção e tubulações do sistema de carga-descarga estão totalmente fixadas com parafusos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las tuberias de venteo, tapas de registro y las tuberias del sistema de carga-descarga estan totalmente fijados con pernos?',
            '¿A barreira de contenção possui tampões de embornais herméticos?' => $flagBR . '¿A barreira de contenção possui tampões de embornais herméticos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La barrera de contención cuenta con tapones de imbornal herméticos?',
            '¿A soldagem da barreira de contenção do convés apresenta continuidade, integridade e acabamento adequados?' => $flagBR . '¿A soldagem da barreira de contenção do convés apresenta continuidade, integridade e acabamento adequados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La soldadura de la barrera de contención de cubierta presenta continuidad, integridad y acabado adecuados?',
            '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?' => $flagBR . '¿Cada tanque possui alarme visual e acústico de alto nível, verificado periodicamente?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cada tanque cuenta con alarma visual y acústica de alto nivel?',
            '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?' => $flagBR . '¿As embarcações com bombas de carga possuem manômetros no coletor e na saída?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las embarcaciones con bombas de carga cuentan con manómetros en el colector y en la salida?',
            '¿O quadro de controle do sistema de alarme encontra-se em bom estado?' => $flagBR . '¿O quadro de controle do sistema de alarme encontra-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El Tablero de control del sistema de alarma se encuentra en buen estado?',
            '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?' => $flagBR . '¿O cabeamento elétrico, conexões e conectores encontram-se em bom estado, estão corretamente isolados, apresentam hermeticidade e proporcionam uma conexão segura e confiável?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La acometida del cableado electrico, conexiones, conectores se encuentran en buen estado, están correctamente aislados, tienen hermeticidad y proporcionan una conexión segura y fiable?',
            '¿As baterias encontram-se em bom estado, instaladas em caixa de segurança e com terminais devidamente protegidos?' => $flagBR . '¿As baterias encontram-se em bom estado, instaladas em caixa de segurança e com terminais devidamente protegidos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las baterías se encuentran en buen estado, instaladas en caja de seguridad y con bornes debidamente protegidos?',

            // PARTE 5 - LUZES DE NAVEGAÇÃO E SINALIZAÇÃO (BARCAZA)
            '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?' => $flagBR . '¿O convés dispõe de plataformas de acesso sobre as tubulações e pintura antiderrapante em todo o trajeto, além de sinalização visível?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La cubierta dispone de plataformas de acceso sobre las tuberías y pintura antideslizante en todo el trayecto y señalización visible?',
            '¿Os equipamentos de FF e LSA estão devidamente identificados com o nome e porto de registro, e possuem uma base adequada para sua estiva?' => $flagBR . '¿Os equipamentos de FF e LSA estão devidamente identificados com o nome e porto de registro, e possuem uma base adequada para sua estiva?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los equipos de FF y LSA están debidamente identificados con el nombre y puerto de registro, y cuentan con una base adecuada para su estiba?',
            '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?' => $flagBR . '¿Os equipamentos de FF e LSA encontram-se em bom estado e/ou operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los equipos de FF y LSA se encuentran en buen estado y/u operativas?',
            '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?' => $flagBR . '¿Os anéis salva-vidas apresentam rabicho, cabo de vida e fita refletiva adequada?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los aros salvavidas presentan rabiza, guirnalda y cinta reflectiva adecuada?',
            '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?' => $flagBR . '¿A embarcação conta com pelo menos dois anéis salva-vidas (localizados a vante e a ré) e dois extintores portáteis disponíveis durante todas as operações (independentes do equipamento permanente das embarcações de apoio), respeitando a quantidade estabelecida em seu certificado estatutário e/ou plano de segurança?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La embarcación cuenta con al menos dos aros salvavidas (ubicados a proa y popa) y dos extintores portátiles disponible durante todas las operaciones (Independientes del equipo permanente de los buques de asistencia), y respetando la cantidad establecida en su certificado estatutario y/o plano de seguridad?',
            '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?' => $flagBR . '¿Existem no convés sinais visíveis que indiquem: áreas de fumantes/não fumantes, Proibição de chama aberta, Acesso restrito a pessoal não autorizado, Carga perigosa, Restrição ao uso de dispositivos não intrinsecamente seguros, sinais de proteção ambiental (proibido lançar resíduos ao mar)?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Existen en cubierta señales visibles que indiquen: areas de fumadores/no fumadores, Prohibición de luz al descubierto, Acceso restringido a personal no autorizado, Carga peligrosa, Restricción de uso de dispositivos no intrínsecamente seguros, señales de protección ambiental (no arrojar residuos al agua)?',
            '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?' => $flagBR . '¿A embarcação conta com Kit de Resposta a Derrames, devidamente identificado e com capacidade suficiente para controlar pequenos vazamentos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La embarcación cuenta con Kit de Respuesta a Derrames, debidamente identificado y con capacidad suficiente para controlar fugas menores?',
            '¿As máquinas e peças móveis a bordo estão providas de proteções ou coberturas de segurança?' => $flagBR . '¿As máquinas e peças móveis a bordo estão providas de proteções ou coberturas de segurança?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las máquinas y piezas móviles a bordo están provistas de resguardos o cubiertas de seguridad?',
            '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?' => $flagBR . '¿O casco, convés e/ou casa de bombas estão livres de manchas de óleo/hidrocarbonetos?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El casco, cubierta y/o sala de bombas estan libre de manchas de aceite/hidrocarburos?',
            '¿O revestimento (pintura) do convés encontra-se em bom estado?' => $flagBR . '¿O revestimento (pintura) do convés encontra-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El recubrimiento (Pintura) de la cubierta está en buen estado?',
            '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?' => $flagBR . '¿As áreas de trânsito estão claramente sinalizadas com pintura antiderrapante?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las zonas de tránsito están claramente señalizadas con pintura antideslizante?',
            '¿Apresenta Luz de Navegação Bombordo – Vermelha em boas condições?' => $flagBR . '¿Apresenta Luz de Navegação Bombordo – Vermelha em boas condições?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta Luz de Situación Babor - Rojo en buenas condiciones?',
            '¿Apresenta Luz de Navegação Boreste – Verde em boas condições?' => $flagBR . '¿Apresenta Luz de Navegação Boreste – Verde em boas condições?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta Luz de Situación Estribor - Verde en buenas condiciones?',
            '¿Apresenta Luz de Topo – Branca em boas condições?' => $flagBR . '¿Apresenta Luz de Topo – Branca em boas condições?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta Luz de Tope - Blanca en buenas condiciones?',
            '¿O sistema de escape do motor está corretamente isolado em toda a sua extensão?' => $flagBR . '¿O sistema de escape do motor está corretamente isolado em toda a sua extensão?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El sistema de escape del motor está correctamente aislado en toda su extensión?',
            '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?' => $flagBR . '¿O arco de visibilidade das luzes de navegação está de acordo com o RIPEAM?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El arco de visibilidad de las luces de navegación esta acorde con el RIPA?',
            '¿A base das luzes de navegação encontra-se em bom estado?' => $flagBR . '¿A base das luzes de navegação encontra-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La base de las luces de navegación se encuentra en buen estado?',
            '¿As escadas de acesso ao convés de carga encontram-se em bom estado?' => $flagBR . '¿As escadas de acesso ao convés de carga encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las escaleras de acceso a la cubierta de carga se encuentra en buen estado?',
            '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?' => $flagBR . '¿Os corrimãos no convés de carga, convés principal ou teto da casa de máquinas encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las barandas en cubierta de carga, principal o techo de sala de maquinas se encuentran en buen estado?',

            // PARTE 6 - SISTEMA DE AMARRAÇÃO (BARCAZA)
            '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?' => $flagBR . '¿Os guinchos, cabrestantes, rolos, cabeços de amarração e cunhos estão em boas condições operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los winches, cabestrantes, rodillos, bitas y cornamusas están en buenas condiciones operativas?',
            '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?' => $flagBR . '¿Cada dispositivo ou elemento de amarração está identificado com seu SWL (Safe Working Load) de forma visível?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cada dispositivo o elemento de amarre está identificado con su SWL (Safe Working Load) de forma visible?',
            '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?' => $flagBR . '¿Os elementos de amarração entre barcaça – barcaça e barcaça – empurrador encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los elementos de amarre entre barcaza - barcaza y barcaza - empujador, se encuentran en buen estado?',
            '¿A embarcação dispõe em ambas as bandas de defensas em bom estado?' => $flagBR . '¿A embarcação dispõe em ambas as bandas de defensas em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La embarcación dispone en ambas bandas de defensas en buen estado?',
            '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?' => $flagBR . '¿O emendão de olhal do cabo de amarra encontra-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El empalme de ojo del cable de amarre se encuentra en buen estado?',

            // PARTE 3 - CASCO Y ESTRUTURAS (EMPUJADOR)
            '¿Os tanques de combustível e conformidade apresentam corrosão significativa?' => $flagBR . '¿Os tanques de combustível e conformidade apresentam corrosão significativa?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques de combustible, tanques vacios y cofferdam presentan corrosión importante?',
            '¿Apresenta nome e porto de registro rotulados em ambas as bandas?' => $flagBR . '¿Apresenta nome e porto de registro rotulados em ambas as bandas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta nombre y puerto de registro rotulado en ambas bandas?',
            '¿Apresenta nome e porto de matrícula rotulados na popa?' => $flagBR . '¿Apresenta nome e porto de matrícula rotulados na popa?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta Nombre y puerto de matrícula rotulados en la popa?',
            '¿Apresenta marcas visíveis do disco de Plimsoll em ambas as bandas, de acordo com o certificado de linha máxima de carga?' => $flagBR . '¿Apresenta marcas visíveis do disco de Plimsoll em ambas as bandas, de acordo com o certificado de linha máxima de carga?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta marcas visibles del disco de Plimsoll en ambas bandas, de acuerdo con el certificado de línea máxima de carga?',
            '¿Apresenta marcas visíveis de calado e escalas correspondentes na proa, popa e seção média de ambos os costados?' => $flagBR . '¿Apresenta marcas visíveis de calado e escalas correspondentes na proa, popa e seção média de ambos os costados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta marcas visibles de calado y escalas correspondientes en proa y popa?',
            '¿Os tanques estão rotulados com sua capacidade de carga?' => $flagBR . '¿Os tanques estão rotulados com sua capacidade de carga?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques están rotulados con su capacidad de carga?',
            '¿O casco, anteparas, planchadas e elementos estruturais estão livres de amassados/deformações significativas?' => $flagBR . '¿O casco, anteparas, planchadas e elementos estruturais estão livres de amassados/deformações significativas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El casco, mamparos, planchajes y elementos estructurales estan libre de abolladuras/deformaciones significativas?',
            '¿A soldagem do casco e da estrutura encontra-se em boas condições?' => $flagBR . '¿A soldagem do casco e da estrutura encontra-se em boas condições?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La soldadura del casco y estructura se encuentran en buenas condiciones?',
            '¿O casco, anteparas, planchadas, elementos estruturais e conexões apresentam fissuras, rupturas (furos)?' => $flagBR . '¿O casco, anteparas, planchadas, elementos estruturais e conexões apresentam fissuras, rupturas (furos)?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El casco, mamparos, planchajes, elementos estructurales y conexiones presentan fracturas, roturas (agujeros)?',
            '¿As tubulações de ventilação, sondagem e escotilhas estão em bom estado?' => $flagBR . '¿As tubulações de ventilação, sondagem e escotilhas estão em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las tuberias de venteo, sondaje y las escotillas se encuentran en buen estado?',
            '¿As defensas encontram-se em bom estado?' => $flagBR . '¿As defensas encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las defensas se encuentran en buen estado?',
            '¿Os motores de propulsão possuem controles desde a ponte e sala de máquinas?' => $flagBR . '¿Os motores de propulsão possuem controles desde a ponte e sala de máquinas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los motores de propulsión cuentan con mandos desde el puente y sala de máquinas?',
            '¿Os motores de propulsão estão em bom estado e operacionais?' => $flagBR . '¿Os motores de propulsão estão em bom estado e operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los motores de propulsión están en buen estado y operativos?',
            '¿O botão de parada de emergência está operacional?' => $flagBR . '¿O botão de parada de emergência está operacional?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El boton de parada de emergencia en el puente y sala de maquinas se encuentra operativo?',
            '¿A embarcação conta com sistema de governo principal e auxiliar em condições operacionais?' => $flagBR . '¿A embarcação conta com sistema de governo principal e auxiliar em condições operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La embarcación cuenta con sistema de gobierno principal y auxliar en condiciones operativas?',
            '¿Possui na ponte de comando um indicador de ângulo de pala (axiómetro) em bom estado de funcionamento?' => $flagBR . '¿Possui na ponte de comando um indicador de ângulo de pala (axiómetro) em bom estado de funcionamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta en puente de mando con un indicador de ángulo de pala (axiómetro) en buen estado de funcionamiento?',
            '¿O tanque de óleo hidráulico está em bom estado e possui visor de nível adequado e tubulação de ventilação?' => $flagBR . '¿O tanque de óleo hidráulico está em bom estado e possui visor de nível adequado e tubulação de ventilação?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El tanque de aceite hidraulico está en buen estado y cuenta con un visor de nivel adecuado y tuberia de venteo?',
            '¿As prensa-estopas dos sistemas de governo e propulsão estão corretamente instaladas e garantem estanqueidade?' => $flagBR . '¿As prensa-estopas dos sistemas de governo e propulsão estão corretamente instaladas e garantem estanqueidade?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las prensaestopas de los sistemas de gobierno y propulsión están correctamente instaladas y garantizan hermeticidad?',
            '¿Os tanques de combustível possuem visor de nível adequado, tubulação de ventilação com proteção contra chamas (arresta-chamas) e tubulação de enchimento com bandeja anti-derrame?' => $flagBR . '¿Os tanques de combustível possuem visor de nível adequado, tubulação de ventilação com proteção contra chamas (arresta-chamas) e tubulação de enchimento com bandeja anti-derrame?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques de combustible cuentan visor de nivel adecuada, tuberia de venteo con proteccion contra llamas (arrestaflama) y con tuberia de llenado con bandeja antiderrame?',
            '¿Os tanques de combustível, juntamente com suas tubulações de ventilação, enchimento e tampas de registro, estão rotulados?' => $flagBR . '¿Os tanques de combustível, juntamente com suas tubulações de ventilação, enchimento e tampas de registro, estão rotulados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tanques de combustible junto con sus tuberias de venteo, llenado y sus tapas de registro están rotulados?',
            '¿A embarcação conta com grupos geradores principal e auxiliar em condições operacionais?' => $flagBR . '¿A embarcação conta com grupos geradores principal e auxiliar em condições operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La embarcación cuenta con grupos electrógenos principal y auxiliar en condiciones operativas?',
            '¿Os quadros elétricos e a rede de alimentação encontram-se em bom estado?' => $flagBR . '¿Os quadros elétricos e a rede de alimentação encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los tableros electricos y la acometida se encuentran en buen estado?',
            '¿Apresenta sistemas de água doce, serviços gerais e dejeto em bom estado de funcionamento?' => $flagBR . '¿Apresenta sistemas de água doce, serviços gerais e dejeto em bom estado de funcionamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta sistemas de agua dulce, servicios generales y aguas negras en buen estado de funcionamiento?',
            '¿O tanque de água doce e os serviços gerais estão em bom estado?' => $flagBR . '¿O tanque de água doce e os serviços gerais estão em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El tanque de agua dulce y servicios generales están en buen estado?',
            '¿Apresenta sistemas de esgoto dos compartimentos vazios e porão da sala de máquinas em bom estado de funcionamento?' => $flagBR . '¿Apresenta sistemas de esgoto dos compartimentos vazios e porão da sala de máquinas em bom estado de funcionamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta sistemas de achique de los compartimientos vacios y sentina de la sala de maquinas en buen estado de funcionamiento?',
            '¿Apresenta alarme de porão (sonoro) de nível alto na sala de máquinas em condições operacionais?' => $flagBR . '¿Apresenta alarme de porão (sonoro) de nível alto na sala de máquinas em condições operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta alarma de sentina (sonora) de nivel alto en la sala de maquinas en condiciones operativas?',
            '¿Conta com tanques de retenção do sistema de sentina e do sistema de águas negras?' => $flagBR . '¿Conta com tanques de retenção do sistema de sentina e do sistema de águas negras?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con tanques de retención del sistema de sentina y del sistema de aguas negras?',

            // PARTE 4 - EQUIPAMENTOS E SISTEMAS (EMPUJADOR)
            '¿Apresenta teste de opacidade?' => $flagBR . '¿Apresenta teste de opacidade?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con certificado de Teste de opacidade actualizado?',
            '¿Possui diário de navegação?' => $flagBR . '¿Possui diário de navegação?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con diario de navegacion?',
            '¿Possui diário de máquinas?' => $flagBR . '¿Possui diário de máquinas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con diario de máquinas?',
            '¿Ao menos um tripulante deve estar familiarizado com os equipamentos de rádio?' => $flagBR . '¿Ao menos um tripulante deve estar familiarizado com os equipamentos de rádio?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Ao menos um tripulante está familiarizado com os equipamentos de rádio?',
            '¿A iluminação interna e externa do navio é adequada e encontra-se em bom estado de funcionamento?' => $flagBR . '¿A iluminação interna e externa do navio é adequada e encontra-se em bom estado de funcionamento?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La ilumincación interna y externa del navio es adecuada y se encuentra en buen estado de fucionamiento?',
            '¿Apresenta sistema de iluminação de emergência na sala de máquinas?' => $flagBR . '¿Apresenta sistema de iluminação de emergência na sala de máquinas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta sistema de iluminación de emergencia en sala de maquinas?',
            '¿As luzes de navegação estão operacionais?' => $flagBR . '¿As luzes de navegação estão operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las luces de navegación se encuentran operativas?',
            '¿As baterias dos equipamentos de comunicação e navegação encontram-se em bom estado?' => $flagBR . '¿As baterias dos equipamentos de comunicação e navegação encontram-se em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las baterías de los equipos de comunicación y navegacion se encuentran en buen estado?',
            '¿Os equipamentos de FF e LSA estão devidamente rotulados?' => $flagBR . '¿Os equipamentos de FF e LSA estão devidamente rotulados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los equipos de FF y LSA están debidamente rotulados?',
            '¿Os anéis salva-vidas possuem corda, guirlanda e fita refletiva adequadas?' => $flagBR . '¿Os anéis salva-vidas possuem corda, guirlanda e fita refletiva adequadas?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los aros salvavidas presentan rabiza, guirnalda y cinta reflectiva adecuada?',
            '¿Os anéis salva-vidas possuem luz, apito, faixas refletivas e estão marcados com o nome do barco e seu porto de registro?' => $flagBR . '¿Os anéis salva-vidas possuem luz, apito, faixas refletivas e estão marcados com o nome do barco e seu porto de registro?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los chalecos salvavidas cuentan con luz, silbato, bandas reflectivas y están marcados con nombre del barco y su puerto de registro?',
            '¿A quantidade de anéis salva-vidas e extintores portáteis está de acordo com o plano de segurança?' => $flagBR . '¿A quantidade de anéis salva-vidas e extintores portáteis está de acordo com o plano de segurança?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La cantidad de aros salvavidas y extintores portátiles está acorde al plano de seguridad?',
            '¿Possui sinalização de advertência visível em áreas de risco: "Perigo Combustível" e "Proibido Fumar"?' => $flagBR . '¿Possui sinalização de advertência visível em áreas de risco: "Perigo Combustível" e "Proibido Fumar"?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con señalética de advertencia visible en zonas de riesgo: "Peligro Combustible" y "No Fumar"?',
            '¿Os produtos inflamáveis encontram-se em áreas seguras?' => $flagBR . '¿Os produtos inflamáveis encontram-se em áreas seguras?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Los productos inflamables se encuentran en áreas seguras?',
            '¿Os artefatos de gás e tubulações estão corretamente estibados?' => $flagBR . '¿Os artefatos de gás e tubulações estão corretamente estibados?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Están correctamente estibados los artefactos de gas y tuberías?',
            '¿Possui botão geral de alarme de emergência (sonoro) em bom estado de funcionamento e pelo menos um na ponte de comando?' => $flagBR . '¿Possui botão geral de alarme de emergência (sonoro) em bom estado de funcionamento e pelo menos um na ponte de comando?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Presenta boton general de alarma de emergencia (sonora) en buen estado de funcionamiento y al menos uno está en el puente?',
            '¿O convés e a sala de máquinas estão corretamente iluminadas, ventiladas e livres de obstáculos (limpas e organizadas)?' => $flagBR . '¿O convés e a sala de máquinas estão corretamente iluminadas, ventiladas e livres de obstáculos (limpas e organizadas)?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La cubierta y sala de máquinas está correctamente iluminada, ventilada y libre de obstáculos (limpia y ordenada)?',
            '¿O navio possui embarcação auxiliar?' => $flagBR . '¿O navio possui embarcação auxiliar?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La nave cuenta con una embarcación auxiliar?',
            '¿Possui kit de primeiros socorros?' => $flagBR . '¿Possui kit de primeiros socorros?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Cuenta con botiquin de primeros auxilios?',
            '¿O sistema de combate a incêndio possui bomba exclusiva, manômetro, hidrantes, mangueiras e acoplamentos de abertura rápida em bom estado e estão operacionais?' => $flagBR . '¿O sistema de combate a incêndio possui bomba exclusiva, manômetro, hidrantes, mangueiras e acoplamentos de abertura rápida em bom estado e estão operacionais?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El sistema contraincendio cuenta con bomba exclusiva, manómetro, hidrantes, mangueras y acoples de apertura rápida en buen estado y se encuentran operativas?',
            '¿O revestimento (pintura) da coberta e superestrutura está em bom estado?' => $flagBR . '¿O revestimento (pintura) da coberta e superestrutura está em bom estado?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El recubrimiento (Pintura) de la cubierta y superestructura está en buen estado?',

            // PARTE 5 - SISTEMA DE AMARRAÇÃO (EMPUJADOR)
            '¿O braço de carga com guincho (manual e/ou elétrico) encontra-se operacional e em condições seguras de uso?' => $flagBR . '¿O braço de carga com guincho (manual e/ou elétrico) encontra-se operacional e em condições seguras de uso?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿El brazo de carga con winche (manual y/o eléctrico) se encuentra operativo y en condiciones seguras de uso?',
            '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?' => $flagBR . '¿A capacidade de içamento do braço de carga está marcada com seu SWL (Safe Working Load) de forma visível?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿La capacidad de izaje del brazo de carga está marcada con su SWL (Safe Working Load) de forma visible?',
            '¿As bitas duplas, simples e/ou cornamusas estão instaladas em número suficiente e corretamente distribuídas ao longo da embarcação?' => $flagBR . '¿As bitas duplas, simples e/ou cornamusas estão instaladas em número suficiente e corretamente distribuídas ao longo da embarcação?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Las bitas dobles, simples y/o cornamusas se encuentran instalados en número suficiente y correctamente distribuidos a lo largo de nave?',
            '¿Dispõe de defensas adequadas para as manobras de empuxo e atracação?' => $flagBR . '¿Dispõe de defensas adequadas para as manobras de empuxo e atracação?&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . '¿Dispone de defensas adecuadas para las maniobras de empuje y atraque?',

            // DOCUMENTOS EXCLUSIVOS PARA EMPUJADORES
            'Certificado de controle de Praga' => $flagBR . 'Certificado de controle de Praga&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Fumigación, Desinfección y Desratización',
            'Plano de incêndio' => $flagBR . 'Plano de incêndio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Plano contraincendio',
            'Operador técnico' => $flagBR . 'Operador técnico - DPA&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Operador técnico - DPA',
            'Pessoa Responsável designada' => $flagBR . 'Pessoa Responsável designada&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Persona responsable designada',
            'Crew List' => $flagBR . 'Crew List&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Crew List',
            'Crew List de saida' => $flagBR . 'Crew List de saida&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Crew List de salida',
            'Cartão de tripulação de segurança (CTS)' => $flagBR . 'Cartão de tripulação de segurança (CTS)&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Certificado de Dotación Mínima',
            'Licença de estação de navio' => $flagBR . 'Licença de estação de navio&nbsp;&nbsp;|&nbsp;&nbsp;' . $flagPE . 'Permiso para Operar una Estación de Comunicación de Teleservicio Móvil',
        ];

        return $translations[$itemPT] ?? $itemPT;
    }

    /**
     * Obtener documentos existentes para una embarcación específica
     */
    protected static function getVesselDocuments(?int $vesselId): array
    {
        if (!$vesselId) {
            return [];
        }

        return VesselDocument::where('vessel_id', $vesselId)
            ->where('is_valid', true)
            ->get()
            ->keyBy('document_type')
            ->toArray();
    }

    /**
     * Get the Eloquent query for the resource table
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Check if the current user has the "Armador" role
        if (auth()->user() && auth()->user()->hasRole('Armador')) {
            // For Armador users, only show inspections for barcazas associated with their user account
            $userId = auth()->id();
            
            // Get vessel IDs assigned to this user
            $userVesselIds = Vessel::where('user_id', $userId)->pluck('id')->toArray();
            
            // Filter to only include inspections for these vessels
            $query->whereIn('vessel_id', $userVesselIds);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('📋 Información General de la Inspección')
                    ->description('Complete los datos básicos requeridos para la inspección checklist')
                    ->icon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 2,
                            'lg' => 3,
                        ])
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('🏢 Propietario')
                                    ->options(Owner::all()->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->prefixIcon('heroicon-o-building-office')
                                    ->placeholder('Seleccione el propietario...')
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('vessel_id')
                                    ->label('🚢 Embarcación')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        if (!$ownerId) {
                                            return [];
                                        }
                                        
                                        // For Armador users, only show vessels assigned to their user account
                                        $query = Vessel::where('owner_id', $ownerId);
                                        
                                        if (auth()->user() && auth()->user()->hasRole('Armador')) {
                                            $userId = auth()->id();
                                            $query->where('user_id', $userId);
                                        }
                                        
                                        return $query->pluck('name', 'id');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->prefixIcon('heroicon-o-rectangle-stack')
                                    ->placeholder('Seleccione la embarcación...')
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        if (!$state) {
                                            return;
                                        }
                                        
                                        // Obtener el tipo de embarcación
                                        $vessel = Vessel::find($state);
                                        if (!$vessel || !$vessel->serviceType) {
                                            return;
                                        }
                                        
                                        $vesselType = strtolower($vessel->serviceType->name);
                                        $structure = ChecklistInspection::getDefaultStructure($vesselType);
                                        
                                        // Actualizar cada parte del checklist
                                        for ($i = 1; $i <= 6; $i++) {
                                            $set("parte_{$i}_items", $structure["parte_{$i}"]);
                                        }
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_start_date')
                                    ->label('📅 Fecha de Inicio de Inspección')
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\DatePicker::make('inspection_end_date')
                                    ->label('📅 Fecha de Fin de Inspección')
                                    ->required()
                                    ->prefixIcon('heroicon-o-calendar-days')
                                    ->displayFormat('d/m/Y')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                Forms\Components\Select::make('inspector_name')
                                    ->label('👷 Inspector Asignado')
                                    ->options(function () {
                                        return User::role('Inspector')
                                            ->orderBy('name')
                                            ->pluck('name', 'name');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->prefixIcon('heroicon-o-user')
                                    ->placeholder('Seleccione el inspector...')
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 1,
                                        'lg' => 1,
                                    ]),

                                // Forms\Components\TextInput::make('inspector_license')
                                //     ->label('📜 Licencia del Inspector')
                                //     ->required()
                                //     ->maxLength(255)
                                //     ->prefixIcon('heroicon-o-identification')
                                //     ->placeholder('Número de licencia...')
                                //     ->columnSpan([
                                //         'default' => 1,
                                //         'md' => 1,
                                //         'lg' => 1,
                                //     ]),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),

                Tabs::make('Checklist de Inspección')
                    ->columnSpanFull()
                    ->tabs([
                        Tabs\Tab::make('🔍 Parte 1')
                            ->label('🇧🇷 DOCUMENTOS DE BANDEIRA E APÓLICES DE SEGURO | 🇵🇪 DOCUMENTOS DE BANDERA Y PÓLIZAS')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_1_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_1_items', '📋 Items de Evaluación - Parte 1', 1),
                            ]),

                        Tabs\Tab::make('⚙️ Parte 2')
                            ->label('🇧🇷 DOCUMENTOS DO SISTEMA DE GESTÃO DE BORDO | 🇵🇪 DOCUMENTOS DEL SISTEMA DE GESTIÓN A BORDO')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_2_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_2_items', '🔧 Items de Evaluación - Parte 2', 2),
                            ]),

                        Tabs\Tab::make('🛡️ Parte 3')
                            ->label('🇧🇷 CASCO E ESTRUTURAS / MÁQUINAS | 🇵🇪 CASCO Y ESTRUCTURAS / MÁQUINAS')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_3_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_3_items', '🛡️ Items de Evaluación - Parte 3', 3, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('📊 Parte 4')
                            ->label('🇧🇷 SISTEMAS DE CARGA E DESCARGA / SEGURANÇA | 🇵🇪 SISTEMAS DE CARGA Y DESCARGA / SEGURIDAD')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_4_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_4_items', '📊 Items de Evaluación - Parte 4', 4, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('🔧 Parte 5')
                            ->label('🇧🇷 SEGURANÇA E LUZES DE NAVEGAÇÃO | 🇵🇪 SEGURIDAD Y LUCES DE NAVEGACIÓN')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_5_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_5_items', '🔧 Items de Evaluación - Parte 5', 5, true), // true for image-only attachments
                            ]),

                        Tabs\Tab::make('✅ Parte 6')
                            ->label('🇧🇷 SISTEMA DE AMARRAÇÃO | 🇵🇪 SISTEMA DE AMARRE')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->badge(fn (Forms\Get $get): int => count($get('parte_6_items') ?? []))
                            ->schema([
                                static::createChecklistSection('parte_6_items', '✅ Items de Evaluación - Parte 6', 6, true), // true for image-only attachments
                            ]),
                    ]),

                Section::make('📊 Evaluación General y Conclusiones')
                    ->description('Resumen final de la inspección y observaciones generales')
                    ->icon('heroicon-o-document-text')
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\Placeholder::make('status_info')
                            ->label('📊 Estado Calculado Automáticamente')
                            ->content('El estado general se calcula automáticamente basado en todos los ítems evaluados')
                            ->extraAttributes([
                                'class' => 'text-sm text-gray-600 bg-blue-50 p-3 rounded-md border border-blue-200'
                            ]),
                            
                        Forms\Components\Textarea::make('general_observations')
                            ->label('📝 Observaciones Generales')
                            ->placeholder('Registre aquí las observaciones generales de la inspección, recomendaciones, puntos importantes a destacar, seguimientos necesarios, etc...')
                            ->rows(6)
                            ->columnSpanFull()
                            ->extraAttributes([
                                'style' => 'resize: vertical; min-height: 120px;'
                            ])
                            ->helperText('ℹ️ Esta sección es para observaciones que aplican a toda la inspección en general'),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
            ]);
    }

    protected static function createChecklistSection(string $fieldName, string $title, int $parteNumber, bool $imageOnly = false): Repeater
    {
        return Repeater::make($fieldName)
            ->label($title)
            ->schema([
                Section::make()
                    ->schema([
                        Grid::make([
                            'default' => 1,
                            'sm' => 1,
                            'md' => 12,
                            'lg' => 12,
                            'xl' => 12,
                        ])
                            ->schema([
                                // Prioridad (no editable) - Usando Placeholder
                                // Forms\Components\Placeholder::make('prioridad_display')
                                //     ->label('🏅 Prioridad')
                                //     ->content(function (Forms\Get $get) {
                                //         $prioridad = $get('prioridad') ?? 3;
                                //         return match($prioridad) {
                                //             1 => '🔴 Crítica',
                                //             2 => '🟡 Alta',
                                //             3 => '🟢 Media',
                                //             default => 'Sin prioridad'
                                //         };
                                //     })
                                //     ->extraAttributes(function (Forms\Get $get) {
                                //         $prioridad = $get('prioridad') ?? 3;
                                //         $colorClass = match($prioridad) {
                                //             1 => 'text-red-600 bg-red-50 border border-red-200',
                                //             2 => 'text-yellow-600 bg-yellow-50 border border-yellow-200',
                                //             3 => 'text-green-600 bg-green-50 border border-green-200',
                                //             default => 'text-gray-600 bg-gray-50 border border-gray-200'
                                //         };
                                //         return [
                                //             'class' => 'font-semibold px-3 py-2 rounded-md ' . $colorClass
                                //         ];
                                //     })
                                //     ->columnSpan([
                                //         'default' => 1,
                                //         'md' => 2,
                                //         'lg' => 2,
                                //     ]),

                                // Sección de verificación con checkboxes mejorados
                                Section::make()
                                    ->schema([
                                        Grid::make([
                                            'default' => 1,
                                            'md' => 2,
                                        ])
                                            ->schema([
                                                Forms\Components\Checkbox::make('checkbox_1')
                                                    ->label('✅ Cumple')
                                                    ->inline(true)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                        // Si se marca "Cumple", desmarcar "No Cumple" y establecer estado APTO
                                                        if ($state === true) {
                                                            $set('checkbox_2', false);
                                                            $set('estado', 'A'); // A = APTO
                                                        } elseif ($state === false && !$get('checkbox_2')) {
                                                            // Si se desmarca y el otro tampoco está marcado, limpiar estado
                                                            $set('estado', '');
                                                        }
                                                    }),

                                                Forms\Components\Checkbox::make('checkbox_2')
                                                    ->label('❌ No Cumple')
                                                    ->inline(true)
                                                    ->live()
                                                    ->afterStateUpdated(function (Forms\Set $set, $state, Forms\Get $get) {
                                                        // Si se marca "No Cumple", desmarcar "Cumple" y calcular estado según prioridad
                                                        if ($state === true) {
                                                            $set('checkbox_1', false);
                                                            $prioridad = $get('prioridad') ?? 3;
                                                            if ($prioridad === 1) {
                                                                $set('estado', 'N'); // N = NO APTO (Prioridad 1)
                                                            } else {
                                                                $set('estado', 'O'); // O = OBSERVADO (Prioridad 2-3)
                                                            }
                                                        } elseif ($state === false && !$get('checkbox_1')) {
                                                            // Si se desmarca y el otro tampoco está marcado, limpiar estado
                                                            $set('estado', '');
                                                        }
                                                    }),
                                            ]),
                                    ])
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 5,
                                        'lg' => 5,
                                    ])
                                    ->compact(),

                                // Estado con colores
                                Forms\Components\Select::make('estado')
                                    ->label('📊 Estado de Evaluación')
                                    ->options([
                                        'A' => 'APTO - Cumple con los requisitos',
                                        'N' => 'NO APTO - No cumple (Prioridad 1)',
                                        'O' => 'OBSERVADO - No cumple (Prioridad 2-3)',
                                    ])
                                    ->prefixIcon('heroicon-o-flag')
                                    ->placeholder('Seleccione el estado...')
                                    ->disabled(function (Forms\Get $get) {
                                        // Deshabilitar si algún checkbox está marcado (estado automático)
                                        return $get('checkbox_1') === true || $get('checkbox_2') === true;
                                    })
                                    ->helperText(function (Forms\Get $get) {
                                        if ($get('checkbox_1') === true || $get('checkbox_2') === true) {
                                            return '✓ Estado establecido automáticamente según evaluación';
                                        }
                                        return 'Seleccione el estado correspondiente o use los checkboxes de evaluación';
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 4,
                                        'lg' => 4,
                                    ]),

                                // Archivos adjuntos o vista de documento existente
                                Forms\Components\FileUpload::make('archivos_adjuntos')
                                    ->label(function (Forms\Get $get) use ($imageOnly) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return '📁 Archivos Adjuntos';
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                return '📄 Documento: ' . $document->getDisplayName();
                                            }
                                        }
                                        
                                        return '📁 Archivos Adjuntos';
                                    })
                                    ->helperText(function (Forms\Get $get) use ($imageOnly) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return 'Suba archivos si es necesario';
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                $statusText = $document->getStatusText();
                                                return "✅ Estado: {$statusText}";
                                            }
                                        }
                                        
                                        // Texto con limitaciones técnicas cuando NO hay documento existente
                                        return $imageOnly ? 'archivos permitidos • JPG, PNG | Peso máx: 10MB' : 'archivos permitidos • PDF, JPG, PNG | Peso máx: 10MB';
                                    })
                                    ->multiple()
                                    ->acceptedFileTypes($imageOnly ? ['image/jpeg', 'image/png', 'image/jpg'] : ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                    ->maxFiles(5)
                                    ->maxSize(10240) // 10MB
                                    ->directory('checklist-attachments')
                                    ->visibility('private')
                                    ->downloadable()
                                    ->previewable()
                                    ->disabled(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return false;
                                        }
                                        
                                        // Deshabilitar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            return VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        return false;
                                    })
                                    ->visible(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return true;
                                        }
                                        
                                        // Verificar si existe documento
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            // Hide if document exists
                                            return !VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        $prioridad = $get('prioridad') ?? 3;
                                        return ChecklistInspection::priorityAllowsAttachments($prioridad);
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 3,
                                        'lg' => 3,
                                    ]),
                                    
                                // Información sobre documento existente
                                Forms\Components\TextInput::make('document_info')
                                    ->label('Documento Existente')
                                    ->placeholder('Descargar  ->')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return '';
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document) {
                                                $statusText = $document->getStatusText();
                                                return "✅ Estado: {$statusText}";
                                            }
                                        }
                                        
                                        return '';
                                    })
                                    ->suffixAction(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return null;
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            $document = VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->first();
                                            
                                            if ($document && $document->file_path) {
                                                $url = route('documents.download', ['id' => $document->id]);
                                                
                                                return Forms\Components\Actions\Action::make('download')
                                                    ->label('Descargar')
                                                    ->icon('heroicon-o-arrow-down-tray')
                                                    ->color('primary')
                                                    ->url($url)
                                                    ->openUrlInNewTab();
                                            }
                                        }
                                        
                                        return null;
                                    })
                                    ->visible(function (Forms\Get $get) {
                                        $vesselId = $get('../../vessel_id');
                                        $itemName = $get('item');
                                        
                                        if (!$vesselId || !$itemName) {
                                            return false;
                                        }
                                        
                                        $documentMapping = static::getDocumentItemMapping();
                                        $documentType = array_search($itemName, $documentMapping);
                                        
                                        if ($documentType) {
                                            return VesselDocument::where('vessel_id', $vesselId)
                                                ->where('document_type', $documentType)
                                                ->where('is_valid', true)
                                                ->exists();
                                        }
                                        
                                        return false;
                                    })
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 3,
                                        'lg' => 3,
                                    ]),

                                // Comentarios mejorados
                                Forms\Components\Textarea::make('comentarios')
                                    ->label('💬 Observaciones y Comentarios')
                                    //->placeholder('Registre aquí las observaciones específicas, recomendaciones o detalles importantes sobre este ítem...')
                                    ->rows(3)
                                    ->columnSpan([
                                        'default' => 1,
                                        'md' => 12,
                                        'lg' => 12,
                                    ])
                                    ->extraAttributes([
                                        'style' => 'resize: vertical; min-height: 80px;'
                                    ]),
                            ]),
                    ])
                    ->collapsible()
                    ->persistCollapsed(),
            ])
            ->afterStateHydrated(function (Repeater $component, $state, Forms\Get $get) use ($parteNumber) {
                // Si ya hay estado (editando), no sobrescribir
                if (!empty($state)) {
                    return;
                }
                
                // Para nuevos registros, usar estructura por defecto
                $defaultItems = ChecklistInspection::getDefaultStructure()["parte_{$parteNumber}"];
                $component->state($defaultItems);
            })
            ->addActionLabel("➕ Agregar ítem adicional")
            ->addAction(
                fn (\Filament\Forms\Components\Actions\Action $action) => $action
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
            )
            ->reorderable(false)
            ->collapsible()
            ->collapsed(true)
            ->itemLabel(function (array $state) {
                $item = $state['item'] ?? 'Nuevo ítem';
                $itemES = $state['item_es'] ?? null;  // Traducción en español si existe
                $estado = $state['estado'] ?? '';
                $prioridad = $state['prioridad'] ?? 3;

                // Traducir el item para visualización (solo UI, no modifica datos)
                // Si existe 'item_es' en la DB, lo usa; si no, usa el diccionario
                $itemTranslated = static::translateItemForDisplay($item, $itemES);
                
                // Mostrar prioridad como emoji al lado del nombre del ítem
                $prioridadEmoji = match($prioridad) {
                    1 => '🔴',
                    2 => '🟡',
                    3 => '🟢',
                    default => ''
                };
                
                // Retornar HtmlString con contenedor en una sola línea
                return new HtmlString('<div style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' . $prioridadEmoji . ' ' . $itemTranslated . '</div>');
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('owner.name')
                    ->label('Propietario')
                    ->searchable()
                    ->sortable()
                    ->color('success')
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('vessel.name')
                    ->label('Embarcación')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('vessel.serviceType.name')
                    ->label('Tipo de Embarcación')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('inspection_start_date')
                    ->label('Inicio Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspection_end_date')
                    ->label('Fin Inspección')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('inspector_name')
                    ->label('Inspector')
                    ->searchable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\BadgeColumn::make('overall_status')
                    ->label('Estado')
                    ->colors([
                        'success' => ['A'],
                        'warning' => ['O'],
                        'danger' => ['N'],
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'A' => 'APTO',
                        'N' => 'NO APTO',
                        'O' => 'OBSERVADO',
                        default => $state
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('owner_vessel_filter')
                    ->form([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\Select::make('owner_id')
                                    ->label('Propietario')
                                    ->options(Owner::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set) {
                                        $set('vessel_id', null);
                                    }),
                                    
                                Forms\Components\Select::make('vessel_id')
                                    ->label('Embarcación')
                                    ->options(function (Forms\Get $get) {
                                        $ownerId = $get('owner_id');
                                        
                                        $query = Vessel::query();
                                        
                                        // Filtrar por propietario si está seleccionado
                                        if ($ownerId) {
                                            $query->where('owner_id', $ownerId);
                                        }
                                        
                                        // For Armador users, only show vessels assigned to their user account
                                        if (auth()->user() && auth()->user()->hasRole('Armador')) {
                                            $userId = auth()->id();
                                            $query->where('user_id', $userId);
                                        }
                                        
                                        return $query->pluck('name', 'id');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn (Forms\Get $get): bool => !$get('owner_id'))
                                    ->helperText('Primero seleccione un propietario'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['owner_id'],
                                fn (Builder $query, $ownerId): Builder => $query->where('owner_id', $ownerId),
                            )
                            ->when(
                                $data['vessel_id'],
                                fn (Builder $query, $vesselId): Builder => $query->where('vessel_id', $vesselId),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['owner_id'] ?? null) {
                            $owner = Owner::find($data['owner_id']);
                            $indicators['owner_id'] = 'Propietario: ' . $owner?->name;
                        }
                        
                        if ($data['vessel_id'] ?? null) {
                            $vessel = Vessel::find($data['vessel_id']);
                            $indicators['vessel_id'] = 'Embarcación: ' . $vessel?->name;
                        }
                        
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('overall_status')
                    ->label('Estado')
                    ->options([
                        'A' => 'APTO - Conforme General',
                        'N' => 'NO APTO - No Conforme Crítico',
                        'O' => 'OBSERVADO - Conforme con Observaciones',
                    ]),

                Tables\Filters\Filter::make('inspection_date_range')
                    ->form([
                        Forms\Components\DatePicker::make('inspection_date_from')
                            ->label('Desde')
                            ->placeholder('Fecha inicial'),
                        Forms\Components\DatePicker::make('inspection_date_to')
                            ->label('Hasta')
                            ->placeholder('Fecha final'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['inspection_date_from'],
                                fn (Builder $query, $date) => $query->whereDate('inspection_start_date', '>=', $date)
                            )
                            ->when(
                                $data['inspection_date_to'],
                                fn (Builder $query, $date) => $query->whereDate('inspection_end_date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['inspection_date_from'] ?? null) {
                            $indicators['inspection_date_from'] = 'Desde: ' . \Carbon\Carbon::parse($data['inspection_date_from'])->format('d/m/Y');
                        }
                        if ($data['inspection_date_to'] ?? null) {
                            $indicators['inspection_date_to'] = 'Hasta: ' . \Carbon\Carbon::parse($data['inspection_date_to'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),

                Tables\Filters\SelectFilter::make('inspector_name')
                    ->label('Inspector')
                    ->options(function () {
                        return User::role('Inspector')
                            ->orderBy('name')
                            ->pluck('name', 'name');
                    })
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(3)
            ->filtersTriggerAction(
                fn (Tables\Actions\Action $action) => $action
                    ->button()
                    ->label('Filtros')
                    ->icon('heroicon-o-funnel')
                    ->color('gray')
            )
            ->deferFilters()
            ->actions([
                Tables\Actions\Action::make('download_pdf')
                    ->label('Descargar PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (ChecklistInspection $record) {
                        return static::downloadPDF($record);
                    })
                    ->visible(function (ChecklistInspection $record): bool {
                        return $record->isValidChecklistStructure();
                    }),

                Tables\Actions\Action::make('generate_new_checklist')
                    ->label('Generar Nuevo Checklist')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->tooltip('Este checklist es antiguo. Crea uno nuevo para poder descargar PDF.')
                    ->url(function (ChecklistInspection $record): string {
                        return route('filament.admin.resources.checklist-inspections.create', [
                            'vessel_id' => $record->vessel_id,
                            'owner_id' => $record->owner_id,
                        ]);
                    })
                    ->visible(function (ChecklistInspection $record): bool {
                        return !$record->isValidChecklistStructure();
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateHeading('No hay inspecciones checklist registradas')
            ->emptyStateDescription('Crea la primera inspección checklist para comenzar.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('Crear inspección')
                    ->url(route('filament.admin.resources.checklist-inspections.create'))
                    ->icon('heroicon-o-plus')
                    ->color('primary')
                    ->size('lg')
                    ->button(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        // Check if the current user has the "Armador" role
        $user = auth()->user();
        
        if ($user && $user->hasRole('Armador')) {
            return false; // Hide create button for Armador role
        }
        
        return true; // Allow create for all other roles
    }

    public static function canDelete($record): bool
    {
        // Check if the current user has the "Armador" role
        $user = auth()->user();
        
        if ($user && $user->hasRole('Armador')) {
            return false; // Hide delete button for Armador role
        }
        
        return true; // Allow delete for all other roles
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    /**
     * Genera y descarga el PDF de la inspección
     */
    public static function downloadPDF(ChecklistInspection $inspection)
    {
        // Preparar datos para el PDF
        $partes = [];
        $stats = [
            'apto' => 0,
            'no_apto' => 0,
            'observado' => 0,
            'total' => 0,
        ];

        // Títulos de las partes (bilingües)
        $parteTitles = [
            1 => 'Parte 1: DOCUMENTOS DE BANDEIRA E APÓLICES | DOCUMENTOS DE BANDERA Y PÓLIZAS',
            2 => 'Parte 2: DOCUMENTOS DO SISTEMA DE GESTÃO | DOCUMENTOS DEL SISTEMA DE GESTIÓN A BORDO',
            3 => 'Parte 3: CASCO E ESTRUTURAS / MÁQUINAS | CASCO Y ESTRUCTURAS / MÁQUINAS',
            4 => 'Parte 4: SISTEMAS DE CARGA E DESCARGA / SEGURANÇA | SISTEMAS DE CARGA Y DESCARGA / SEGURIDAD',
            5 => 'Parte 5: SEGURANÇA E LUZES DE NAVEGAÇÃO | SEGURIDAD Y LUCES DE NAVEGACIÓN',
            6 => 'Parte 6: SISTEMA DE AMARRAÇÃO | SISTEMA DE AMARRE',
        ];

        // Recopilar datos de cada parte
        for ($i = 1; $i <= 6; $i++) {
            $items = $inspection->getAttribute('parte_' . $i . '_items') ?? [];

            if (!empty($items)) {
                $partes[$i] = [
                    'title' => $parteTitles[$i],
                    'items' => $items,
                ];

                // Contar estados
                foreach ($items as $item) {
                    $estado = $item['estado'] ?? '';
                    if (!empty($estado)) {
                        $stats['total']++;
                        if ($estado === 'A') $stats['apto']++;
                        elseif ($estado === 'N') $stats['no_apto']++;
                        elseif ($estado === 'O') $stats['observado']++;
                    }
                }
            }
        }

        // Generar PDF
        $pdf = Pdf::loadView('pdf.checklist-inspection', [
            'inspection' => $inspection,
            'partes' => $partes,
            'stats' => $stats,
        ]);

        // Configurar PDF
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);

        // Nombre del archivo
        $fileName = 'Inspeccion_' . $inspection->vessel->name . '_' . now()->format('Y-m-d') . '.pdf';
        $fileName = str_replace(' ', '_', $fileName);

        // Guardar en public/pdfs/
        $publicPath = public_path('pdfs');
        if (!file_exists($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        $filePath = $publicPath . '/' . $fileName;
        $pdf->save($filePath);

        // Descargar
        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChecklistInspections::route('/'),
            'create' => Pages\CreateChecklistInspection::route('/create'),
            'view' => Pages\ViewChecklistInspection::route('/{record}'),
            'edit' => Pages\EditChecklistInspection::route('/{record}/edit'),
        ];
    }
}