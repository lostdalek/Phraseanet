<?php
/*
 * This file is part of Phraseanet
 *
 * (c) 2005-2016 Alchemy
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Alchemy\Phrasea\Controller\Prod;

use Alchemy\Phrasea\Application;
use Alchemy\Phrasea\Application\Helper\SearchEngineAware;
use Alchemy\Phrasea\Cache\Exception;
use Alchemy\Phrasea\Controller\Controller;
use Alchemy\Phrasea\Core\Configuration\DisplaySettingService;
use Alchemy\Phrasea\Model\Entities\ElasticsearchRecord;
use Alchemy\Phrasea\SearchEngine\SearchEngineOptions;
use Alchemy\Phrasea\SearchEngine\SearchEngineResult;
use Alchemy\Phrasea\Twig\PhraseanetExtension;
use Alchemy\Phrasea\Utilities\StringHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class QueryController extends Controller
{
    use SearchEngineAware;

    /**
     * Query Phraseanet to fetch records
     *
     * @param  Request $request
     * @return Response
     */
    public function query(Request $request)
    {
        $query = (string) $request->request->get('qry');

        // since the query comes from a submited form, normalize crlf,cr,lf ...
        $query = StringHelper::crlfNormalize($query);

        $json = array(
            'query' => $query
        );

        $options = SearchEngineOptions::fromRequest($this->app, $request);

        $perPage = (int) $this->getSettings()->getUserSetting($this->getAuthenticatedUser(), 'images_per_page');

        $page = (int) $request->request->get('pag');
        $firstPage = $page < 1;

        $engine = $this->getSearchEngine();
        if ($page < 1) {
            $engine->resetCache();
            $page = 1;
        }

        $options->setFirstResult(($page - 1) * $perPage);
        $options->setMaxResults($perPage);

        $user = $this->getAuthenticatedUser();
        $userManipulator = $this->getUserManipulator();
        $userManipulator->logQuery($user, $query);

        try {
            $result = $engine->query($query, $options);

            if ($this->getSettings()->getUserSetting($user, 'start_page') === 'LAST_QUERY') {
                $userManipulator->setUserSetting($user, 'start_page_query', $query);
            }

            foreach ($options->getDataboxes() as $databox) {
                $collections = array_map(function (\collection $collection) {
                    return $collection->get_coll_id();
                }, array_filter($options->getCollections(), function (\collection $collection) use ($databox) {
                    return $collection->get_databox()->get_sbas_id() == $databox->get_sbas_id();
                }));

                $this->getSearchEngineLogger()->log($databox, $result->getQuery(), $result->getTotal(), $collections);
            }

            $proposals = $firstPage ? $result->getProposals() : false;

            $npages = $result->getTotalPages($perPage);

            $page = $result->getCurrentPage($perPage);

            $string = '';

            if ($npages > 1) {
                $d2top = ($npages - $page);
                $d2bottom = $page;

                if (min($d2top, $d2bottom) < 4) {
                    if ($d2bottom < 4) {
                        if($page != 1){
                            $string .= "<a id='PREV_PAGE' class='btn btn-primary btn-mini'></a>";
                        }
                        for ($i = 1; ($i <= 4 && (($i <= $npages) === true)); $i++) {
                            if ($i == $page)
                                $string .= '<input type="text" value="' . $i . '" size="' . (strlen((string) $i)) . '" class="btn btn-mini search-navigate-input-action" data-initial-value="' . $i . '" data-total-pages="'.$npages.'"/>';
                            else
                                $string .= '<a class="btn btn-primary btn-mini search-navigate-action" data-page="'.$i.'">' . $i . '</a>';
                        }
                        if ($npages > 4)
                            $string .= "<a id='NEXT_PAGE' class='btn btn-primary btn-mini'></a>";
                        $string .= '<a href="#" class="btn btn-primary btn-mini search-navigate-action" data-page="' . $npages . '" id="last"></a>';
                    } else {
                        $start = $npages - 4;
                        if (($start) > 0){
                            $string .= '<a class="btn btn-primary btn-mini search-navigate-action" data-page="1" id="first"></a>';
                            $string .= '<a id="PREV_PAGE" class="btn btn-primary btn-mini"></a>';
                        }else
                            $start = 1;
                        for ($i = ($start); $i <= $npages; $i++) {
                            if ($i == $page)
                                $string .= '<input type="text" value="' . $i . '" size="' . (strlen((string) $i)) . '" class="btn btn-mini search-navigate-input-action" data-initial-value="' . $i . '" data-total-pages="'.$npages.'" />';
                            else
                                $string .= '<a class="btn btn-primary btn-mini search-navigate-action" data-page="'.$i.'">' . $i . '</a>';
                        }
                        if($page < $npages){
                            $string .= "<a id='NEXT_PAGE' class='btn btn-primary btn-mini'></a>";
                        }
                    }
                } else {
                    $string .= '<a class="btn btn-primary btn-mini btn-mini search-navigate-action" data-page="1" id="first"></a>';

                    for ($i = ($page - 2); $i <= ($page + 2); $i++) {
                        if ($i == $page)
                            $string .= '<input type="text" value="' . $i . '" size="' . (strlen((string) $i)) . '" class="btn btn-mini search-navigate-input-action" data-initial-value="' . $i . '" data-total-pages="'.$npages.'" />';
                        else
                            $string .= '<a class="btn btn-primary btn-mini search-navigate-action" data-page="'.$i.'">' . $i . '</a>';
                    }

                    $string .= '<a href="#" class="btn btn-primary btn-mini search-navigate-action" data-page="' . $npages . '" id="last"></a>';
                }
            }
            $string .= '<div style="display:none;"><div id="NEXT_PAGE"></div><div id="PREV_PAGE"></div></div>';

            $explain = "<div id=\"explainResults\" class=\"myexplain\">";

            $explain .= "<img src=\"/assets/common/images/icons/answers.gif\" /><span><b>";

            if ($result->getTotal() != $result->getAvailable()) {
                $explain .= $this->app->trans('reponses:: %available% Resultats rappatries sur un total de %total% trouves', ['available' => $result->getAvailable(), '%total%' => $result->getTotal()]);
            } else {
                $explain .= $this->app->trans('reponses:: %total% Resultats', ['%total%' => $result->getTotal()]);
            }

            $explain .= " </b></span>";
            $explain .= '<br><div>' . ($result->getDuration() / 1000) . ' s</div>dans index ' . $result->getIndexes();
            $explain .= "</div>";

            $infoResult = '<div id="docInfo">'
                . $this->app->trans('%number% documents<br/>selectionnes', ['%number%' => '<span id="nbrecsel"></span>'])
                . '</div><a href="#" class="infoDialog search-display-info" data-infos="' . str_replace('"', '&quot;', $explain) . '">'
                . $this->app->trans('%total% reponses', ['%total%' => '<span>'.$result->getTotal().'</span>']) . '</a>';

            $json['infos'] = $infoResult;
            $json['navigationTpl'] = $string;
            $json['navigation'] = [
                'page' => $page,
                'perPage' => $perPage
            ];

            $prop = null;

            if ($firstPage) {
                $propals = $result->getSuggestions();
                if (count($propals) > 0) {
                    foreach ($propals as $prop_array) {
                        if ($prop_array->getSuggestion() !== $query && $prop_array->getHits() > $result->getTotal()) {
                            $prop = $prop_array->getSuggestion();
                            break;
                        }
                    }
                }
            }

            if ($result->getTotal() === 0) {
                $template = 'prod/results/help.html.twig';
            } else {
                $template = 'prod/results/records.html.twig';
            }

            $json['results'] = $this->render($template, ['results'=> $result]);
            $json['recordCollection'] = $this->processQueryResult($result);
              /** Debug */
            $json['parsed_query'] = $result->getQuery();
            /** End debug */

            $fieldLabels = [
                'Base_Name' => $this->app->trans('prod::facet:base_label'),
                'Collection_Name' => $this->app->trans('prod::facet:collection_label'),
                'Type_Name' => $this->app->trans('prod::facet:doctype_label'),
            ];
            foreach ($this->app->getDataboxes() as $databox) {
                foreach ($databox->get_meta_structure() as $field) {
                    if (!isset($fieldLabels[$field->get_name()])) {
                        $fieldLabels[$field->get_name()] = $field->get_label($this->app['locale']);
                    }
                }
            }

            $facets = [];

            foreach ($result->getFacets() as $facet) {
                $facetName = $facet['name'];

                $facet['label'] = isset($fieldLabels[$facetName]) ? $fieldLabels[$facetName] : $facetName;

                $facets[] = $facet;
            }

            $json['facets'] = $facets;
            $json['phrasea_props'] = $proposals;
            $json['total_answers'] = (int) $result->getAvailable();
            $json['next_page'] = ($page < $npages && $result->getAvailable() > 0) ? ($page + 1) : false;
            $json['prev_page'] = ($page > 1 && $result->getAvailable() > 0) ? ($page - 1) : false;
            $json['form'] = $options->serialize();
        }
        catch(\Exception $e) {
            // we'd like a message from the parser so get all the exceptions messages
            $msg = '';
            for(; $e; $e=$e->getPrevious()) {
                $msg .= ($msg ? "\n":"") . $e->getMessage();
            }
            $template = 'prod/results/help.html.twig';
            $result = array(
                'error' => $msg
            );
            $json['results'] = $this->render($template, ['results'=> $result]);
        }


        return $this->app->json($json);
    }

    /**
     * Get a preview answer train
     *
     * @param  Request $request
     * @return Response
     */
    public function queryAnswerTrain(Request $request)
    {
        if (null === $optionsSerial = $request->get('options_serial')) {
            $this->app->abort(400, 'Search engine options are missing');
        }

        try {
            $options = SearchEngineOptions::hydrate($this->app, $optionsSerial);
        } catch (\Exception $e) {
            $this->app->abort(400, 'Provided search engine options are not valid');
        }

        $pos = (int) $request->request->get('pos', 0);
        $query = $request->request->get('query', '');

        $record = new \record_preview($this->app, 'RESULT', $pos, '', $this->getSearchEngine(), $query, $options);

        $index = ($pos - 3) < 0 ? 0 : ($pos - 3);
        return $this->app->json([
            'current' => $this->render('prod/preview/result_train.html.twig', [
                'records'  => $record->get_train(),
                'index' => $index,
                'selected' => $pos,
            ])
        ]);
    }

    /**
     * @return DisplaySettingService
     */
    private function getSettings()
    {
        return $this->app['settings'];
    }

    /**
     * @return mixed
     */
    private function getUserManipulator()
    {
        return $this->app['manipulator.user'];

    }

    /**
     * Process results as json array
     * @param $results SearchEngineResult
     */
    private function processQueryResult($results)
    {
        $output = [];
        /* @var $results SearchEngineResult */
        $resultArray = $results->getResults();

        $helpers = new PhraseanetExtension($this->app);
        $images_size = $helpers->getUserSetting('images_size');
        $technical_display = $helpers->getUserSetting('technical_display');
        $rollover_thumbnail = $helpers->getUserSetting('rollover_thumbnail');
        $doctype_display = $helpers->getUserSetting('doctype_display');

        foreach($resultArray as $record) {
            /* @var $record ElasticsearchRecord */


            $can_see_business = $helpers->isGrantedOnCollection($record->getBaseId(), 'canmodifrecord');

            $recordData = [
                'databoxId' => $record->getBaseId(),
                'id' => $record->getId(),
                'title' => $record->getTitle($this->app['locale']),
                'isStory' => $record->isStory(),
                'type' => $record->getType(),
                'position' => $record->getPosition(),
                'caption' => '', // macro.caption(record, can_see_business, false) | e
                'preview' => '', // {{ path('prod_tooltip_preview', { 'sbas_id' : record.databoxId, 'record_id' : record.recordId }) }}
                'url' => (string)$helpers->getThumbnailUrl($record),
                // settings.images_size

            ];
            foreach($helpers->getRecordFlags($record) as $flag) {
                $recordData['flags'][] = [
                  'path' => $flag['path'],
                  'labels' =>$flag['labels']
                  ];
            }


            $output[] = $recordData;
        }
        return $output;
        /*

                {% if settings.doctype_display == '1' %}
                    {{ record_doctype_icon(record) }}
                {% endif %}
                <span class="duration">
                    {% if record.type == 'video' and attribute(record.exif, constant('\\media_subdef::TC_DATA_DURATION')) is defined %}
                        {{ attribute(record.exif, constant('\\media_subdef::TC_DATA_DURATION')) }}
                    {% endif %}
                </span>
            </div>
            {% set rollover = record.subdefs.thumbnailgif is defined %}
            {% set extraclass = '' %}
            <div class="{% if rollover %}rollovable{% endif %}">
                {% if rollover %}
                    {% set extraclass = 'rollover-gif-out' %}
                {% endif %}

                {{ result_macro.thumbnail(record, settings.images_size, settings.images_size, extraclass) }}

                {% if rollover %}
                    {% set extraclass = 'rollover-gif-hover' %}
                    {{ result_macro.thumbnailgif(record, settings.images_size, settings.images_size, extraclass) }}
                {% endif %}
            </div>
        </div>
        <div style="height: 25px; position:relative; text-align:left;">
            <table class="bottom actions" style="width:100%; table-layout:fixed;">
                <tr>
                    <td style="text-align:left;text-overflow:ellipsis;overflow:hidden;">
                        {{ record.collectionName }}
                        {{ collection_logo(record.baseId) }}
                    </td>

                    {% set l_width = 30 %}

                    {% if settings.rollover_thumbnail == 'preview' or settings.rollover_thumbnail == 'caption' %}
                        {% set l_width = l_width + 50 %}
                    {% elseif settings.technical_display == '1' %}
                        {% set l_width = l_width + 50 %}
                    {% endif %}

                    {# drop down options #}
                    <td style="text-align:right;width:{{l_width}}px;" valign="bottom">

                        {% if settings.rollover_thumbnail == 'caption' %}
                            {% if record.subdefs.preview is defined and has_access_subdef(record, 'preview') %}
                                <span class="icon-stack previewTips" tooltipsrc="{{ path('prod_tooltip_preview', { 'sbas_id' : record.databoxId, 'record_id' : record.recordId }) }}" class="previewTips">
                                  <i class="icon-circle icon-stack-base"></i>
                                  <i class="icon-search icon-light"></i>
                                </span>
                            {% endif %}
                        {% endif %}
                        {% if settings.rollover_thumbnail == 'preview' %}
                            <span class="icon-stack captionRolloverTips" tooltipsrc="{{ path('prod_tooltip_caption', { 'sbas_id' : record.databoxId, 'record_id' : record.recordId, 'context' : 'answer', 'number' : record.position|default(0) }) }}">
                              <i class="icon-circle icon-stack-base"></i>
                              <i class="icon-list icon-light"></i>
                            </span>
                        {% endif %}

                        {% if settings.technical_display == '1' %}
                            <span class="icon-stack infoTips" tooltipsrc="{{ path('prod_tooltip_technical_data', { 'sbas_id' : record.databoxId, 'record_id' : record.recordId }) }}">
                              <i class="icon-circle icon-stack-base"></i>
                              <i class="icon-info icon-light"></i>
                            </span>
                        {% endif %}

                        <span class="icon-stack contextMenuTrigger"  id="contextTrigger_{{record.id}}"
                              tooltipsrc="{{ path('prod_tooltip_technical_data', { 'sbas_id' : record.databoxId, 'record_id' : record.recordId }) }}">
                              <i class="icon-circle icon-stack-base"></i>
                              <i class="icon-ellipsis-horizontal icon-light"></i>
                        </span>
                        <table cellspacing="0" cellpadding="0" style="display:none;" id="answerContext_{{record.id}}" class="contextMenu answercontextmenu">
                            <tbody>
                            <tr>
                                <td>
                                    <div class="context-menu context-menu-theme-vista">
                                        {% if granted_on_collection(record.baseId, 'canputinalbum') and not record.story %}
                                            <div title="" class="context-menu-item">
                                                <div class="context-menu-item-inner record-add-to-basket-action" data-db-id="{{record.databoxId}}" data-record-id="{{record.recordId}}">
                                                    {{ 'action : ajouter au panier' | trans }}
                                                </div>
                                            </div>
                                        {% endif %}
                                        {% if granted_on_collection(record.baseId, 'candwnldpreview') or granted_on_collection(record.baseId, 'candwnldhd') %}
                                            <div title="" class="context-menu-item">
                                                <div class="context-menu-item-inner record-export-action" data-kind="record" data-id="{{record.id}}">
                                                    {{ 'action : exporter' | trans }}
                                                </div>
                                            </div>
                                        {% endif %}
                                        {% if app['conf'].get(['registry', 'actions', 'social-tools']) == 'all'
                                        or (app['conf'].get(['registry', 'actions', 'social-tools']) == 'publishers'
                                        and granted_on_databox(record.databoxId, 'bas_chupub')) %}
                                            {% if record.story is empty %}
                                                <div title="" class="context-menu-item">
                                                    <div class="context-menu-item-inner share-record-action" data-db="{{record.baseId}}" data-record-id="{{record.recordId}}">
                                                        {{ 'reponses:: partager' | trans }}
                                                    </div>
                                                </div>
                                            {% endif %}
                                        {% endif %}*/
    }
}
