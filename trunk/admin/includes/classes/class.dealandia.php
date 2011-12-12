<?php
require_once "class.ajaxexporter.php";

class ISC_ADMIN_DEALANDIA extends ISC_ADMIN_AJAXEXPORTER
{
	public function __construct()
	{

			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('froogle');

		$this->exportName = GetLang('FroogleFeed');
		$this->className = 'Froogle';
		$this->displayAutoExport = false;
		$this->exportIcon = 'froogle.gif';

		$GLOBALS['ExportName'] = GetLang('FroogleFeed');
		$GLOBALS['ExportIntro'] = GetLang('FroogleFeedIntro');
		$GLOBALS['ExportGenerate'] = GetLang('GenerateFroogleFeed');

		parent::__construct();
			}

	protected function GetResultCount()
	{
	/*		$query = "
			SELECT COUNT(*) FROM (
				SELECT
					p.productid
				FROM
					[|PREFIX|]products p
					LEFT JOIN [|PREFIX|]categoryassociations ca ON (p.productid=ca.productid)
				WHERE
					p.prodvisible=1
					AND p.proddesc <> ''
				GROUP BY
					p.productid
                                 AND publish_start < sysdate()
                                AND publish_end > sysdate()
			) as count
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$count = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

		return $count;
	*/
		//No lo utilizamos
		return 1;
	}



	protected function WriteHeader()
	{
		/*		$exportDate = isc_date("Y-m-d\TH:i:s\Z", time());
				$header = '<?xml version="1.0" encoding="' . GetConfig('CharacterSet') . '"?>
				<feed xmlns="http://www.w3.org/2005/Atom" xmlns:g="http://base.google.com/ns/1.0">
					<title>' . isc_html_escape($GLOBALS['StoreName']) . '</title>
					<link rel="self" href="'.str_replace("https://", "http://",$GLOBALS['ShopPath']).'"/>
					<updated>'.$exportDate.'</updated>
					<author>
						<name>' . isc_html_escape($GLOBALS['StoreName']) . '</name>
					</author>
					<id>tag:'.time().'</id>
				';
		*/
		fwrite($this->handle, '<?xml version="1.0" encoding="UTF-8"?>
                                        <dealandiaApi:city-list
                                        xmlns:dealandiaApi="http://www.dealandia.com/ns/2010/09"
                                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                        xsi:schemaLocation="http://www.dealandia.com/ns/2010/09
                                        http://dealandia.com/schema/2011/06/dealandia-api-schema.xsd">');

	}

	protected function WriteFooter()
	{

		fwrite($this->handle, '</city-deals></dealandiaApi:city-list>');
	}

	protected function GetResult($generateFull = false, $start = 0)
		{
			$query = "
		select a.*, c.categoryid, c.catname from [|PREFIX|]categories c
			inner join
			(
			SELECT
                                p.*,
                                        (SELECT b.brandname FROM [|PREFIX|]brands b WHERE b.brandid=p.prodbrandid) AS brandname,
                                        (SELECT b.brandinfo FROM [|PREFIX|]brands b WHERE b.brandid=p.prodbrandid) AS brandinfo,
                                pi.*
                        FROM
                                [|PREFIX|]products p
                                INNER JOIN [|PREFIX|]categoryassociations ca ON (p.productid = ca.productid)
                                INNER JOIN [|PREFIX|]categories c ON (ca.categoryid = c.categoryid)
                                LEFT JOIN [|PREFIX|]product_images pi ON (pi.imageprodid = p.productid AND pi.imageisthumb = 1)
                        WHERE
                                p.prodvisible=1
                                AND p.proddesc <> ''
                                AND publish_start < sysdate()
                                AND publish_end > sysdate()
                        GROUP BY
                                p.productid
                        ORDER BY
                                c.catname
			) a on  (FIND_IN_SET(c.categoryid, a.prodcatids) > 0)
			 order by c.catname

			";
		if (!$generateFull) {
			$query .= $GLOBALS['ISC_CLASS_DB']->AddLimit($start, ISC_AJAX_EXPORT_PER_PAGE);
			}
			$result = $GLOBALS['ISC_CLASS_DB']->Query($query);

		return $result;
	}
	public function WriteRow($row)
	{
		return false;
	}
	public function WriteDealandiaRow($row, $categ)
	{
		$link = ProdLink($row['prodname']) . "?showcattop=" . str_replace(" ", "-", $row['catname'])  ;
		$addr = substr($row['brandinfo'], 0, strpos($row['brandinfo'], ".-"));		

		$googleAddr = str_replace(" ", "+", $addr);
		$geocode= file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $googleAddr . '&sensor=false');

		$output= json_decode($geocode);

		
		$lat = $output->results[0]->geometry->location->lat;
		if ($lat == ''){$lat = '0.00';}
		$long = $output->results[0]->geometry->location->lng;
		if ($long == ''){$long = '0.00';}

		$disccount = $row['prodretailprice'] - $row['prodprice'];
		
		$finalPrice = number_format($row['prodprice'], 2, '.', '');
		$origPrice = number_format($row['prodretailprice'], 2, '.', '');

            if(!empty($row['imagefile'])) {
                        try {
                                $image = new ISC_PRODUCT_IMAGE();
                                $image->populateFromDatabaseRow($row);
                                }
                        catch (Exception $ex) {
                        }
                }

		$beginTime = new DateTime($row['publish_start']);
		$beginTime = $beginTime->format('Y-m-d\TH:i:s');
                $endTime = new DateTime($row['publish_end']);
                $endTime = $endTime->format('Y-m-d\TH:i:s');

		$entry = array(
			'url' => isc_html_escape($link),
			'imageUrl' => isc_html_escape($image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false)),
			'title' => isc_html_escape($row['prodname']),
			'description' => strip_tags(substr($row['proddesc'], 0, strpos($row['proddesc'], "Condiciones:"))),
			'price' => $finalPrice,
			'marketPrice' => $origPrice,
                        'discount'=> ($disccount * 100)/ $origPrice,
                        'beginTime'=> $beginTime,
                        'endTime'=> $endTime,
			'purchased'=> $row['prodnumsold'],
			'coupon-id' => $row['productid']
		);


               	if(!empty($row['imagefile'])) {
			try {
				$image = new ISC_PRODUCT_IMAGE();
				$image->populateFromDatabaseRow($row);
				$entry['imageUrl'] = isc_html_escape($image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false));
				}
			catch (Exception $ex) {
			}
		}
		if ($row['catname'] != $categ){
	            if ($categ != ''){
			$xml = '</city-deals><city-deals>
			    <coupons-city-name>' . $row['catname'] .'</coupons-city-name>';
		    }else{
                    $xml = '<city-deals>
    <coupons-city-name>' . $row['catname'] .'</coupons-city-name>';
		    }
                    $xml .= "<deal>\n";
                }else{
			$xml = "<deal>\n";
		}
		foreach($entry as $k => $v) {
			$xml .= "\t<".$k."><![CDATA[".$v."]]></".$k.">\n";
		}
                $xml .= '<dealandiaApi:business-locations>
                        <dealandiaApi:business-location address= "' . $addr .'" name="' . $row['brandname'] . '" >
          <dealandiaApi:geo latitude="' . $lat . '" longitude="' . $long . '" />
        </dealandiaApi:business-location>
      </dealandiaApi:business-locations>';
                
		$xml .= "</deal>\n";
		fwrite($this->handle, $xml);
		
	}
}
