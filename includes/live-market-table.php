<?php

$table = array( 'popular' => array(), 'forex' => array(), 'indices' => array(), 'shares' => array(), 'commodities' => array() );
$xml_file = 'charts/commodities.xml';
$symbols = array();

if (file_exists($xml_file)) {
    $xml = simplexml_load_file($xml_file); 
    foreach ($xml->commodity as $com) {
        $temp = array();
        $temp['code'] = (string)$com;
        $temp['display'] = (string) $com['display'];
        $temp['category'] = (string) $com['category'];
        $temp['popular'] = (string) $com['popular'];
        $temp['chartindex'] = (string) $com['chartindex'];
        $symbols[] = $temp;
    }
}

if (!empty($symbols)) {
    foreach ($symbols as $item) { //foreach element in $arr
        $html = '<tr data-marketsymbol="' . $item['code'] . '" data-update=\'{"positive":"bg-success","negative":"bg-danger"}\'>
                    <td class="first-column"><span class="text-nowrap">' . $item['display'] . '</span></td>
                    <td class="text-center" data-field="bid"><span>&nbsp;</span></td>
                    <td class="text-center" data-field="ask"><span>&nbsp;</span></td>
                    <td class="text-center" data-field="high"><span>&nbsp;</span></td>
                    <td class="text-center" data-field="low"><span>&nbsp;</span></td>
                </tr>';
        $table[ $item['category'] ][] = $html;
        if ($item['popular'] === "true") {
            $table['popular'][] = $html;
        }
    }
}

foreach ($table['popular'] as $key => $value) { //foreach element in $arr
    //echo '<div id="' . $key . 'data" class="table-responsive mt-23 tab-pane fade' . ($key === 'popular' ? ' in active' : '') . '">';
    //echo '<table class="table table-live-data" data-table="' . $key . '">';
    //echo '<thead><tr class="text-capitalize"><th><span>Name</span></th><th><span>Bid</span></th><th><span>Ask</span></th><th><span>High</span></th><th><span>Low</span></th></tr></thead>';
    //echo '<tbody>';
    //$newarray = array_pad($value, 5, '');
    //echo implode('', $newarray);
    echo $value;
    //echo '</tbody>';
    //echo '</table>';
    //echo '</div>';
}
?>