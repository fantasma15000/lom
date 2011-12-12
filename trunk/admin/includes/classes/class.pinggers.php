<?php
require_once "class.ajaxexporter.php";

class ISC_ADMIN_PINGGERS extends ISC_ADMIN_AJAXEXPORTER
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
		$query = "
			SELECT COUNT(*) FROM (
				SELECT
					p.productid
				FROM
					[|PREFIX|]products p
					LEFT JOIN [|PREFIX|]categoryassociations ca ON (p.productid=ca.productid)
				WHERE
					p.prodvisible=1
					AND p.proddesc <> ''
                                AND publish_start < sysdate()
                                AND publish_end > sysdate()

				GROUP BY
					p.productid
			) as count
		";
		$result = $GLOBALS['ISC_CLASS_DB']->Query($query);
		$count = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

		return $count;
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
		fwrite($this->handle, '<SERVER>');

	}

	protected function WriteFooter()
	{

		fwrite($this->handle, '</SERVER>');
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
       public function WriteDealandiaRow($row, $categ)
        {
                return false;
        }

	public function WriteRow($row)
	{
		$link = ProdLink($row['prodname'])  . "?showcattop=" . str_replace(" ", "-", $row['catname']) ;

		$addr = substr($row['brandinfo'], 0, strpos($row['brandinfo'], ".-"));		

		$googleAddr = str_replace(" ", "+", $addr);
		$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?address=' . $googleAddr . '&sensor=false');

		$output= json_decode($geocode);

		$lat = $output->results[0]->geometry->location->lat;
		$long = $output->results[0]->geometry->location->lng;


		$disccount = $row['prodretailprice'] - $row['prodprice'];
		
		$finalPrice = number_format($row['prodprice'], 2, '.', '');
		$origPrice = number_format($row['prodretailprice'], 2, '.', '');

		$entry = array(
			'CITY' => isc_html_escape($row['catname']),
			'TITLE' => isc_html_escape($row['prodname']),
			'URL' => isc_html_escape($link),
			'FINAL_PRICE' => $finalPrice,
			'ORIGINAL_PRICE' => $origPrice,
			'CUSTOMERS'=> $row['prodnumsold'],
			'DEAL_START'=> $row['publish_start'],
			'DEAL_END'=> $row['publish_end'],
			'VOUCHER_START'=> $row['publish_start'],
			'VOUCHER_END'=> $row['Validez'],
			'LATITUDE'=> $lat,
			'LONGITUDE'=> $long,
			'DISCOUNT'=> $disccount,
			'PERCENTAGE'=> ($disccount * 100)/ $origPrice,
			'STORE_NAME'=> $row['brandname'],
			'STORE_ADDR'=> $addr,
			'PROD_DESC' => strip_tags(substr($row['proddesc'], 0, strpos($row['proddesc'], "Condiciones:")))
		);


               	if(!empty($row['imagefile'])) {
			try {
				$image = new ISC_PRODUCT_IMAGE();
				$image->populateFromDatabaseRow($row);
				$entry['IMAGE'] = isc_html_escape($image->getResizedUrl(ISC_PRODUCT_IMAGE_SIZE_ZOOM, true, true, false));
				}
			catch (Exception $ex) {
			}
		}

		$xml = "<deal>\n";
		foreach($entry as $k => $v) {
			$xml .= "\t<".$k."><![CDATA[".$v."]]></".$k.">\n";
		}
		$xml .= "</deal>\n";
		fwrite($this->handle, $xml);
		
	}
}
