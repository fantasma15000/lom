<?php

	class ISC_ADMIN_SUCURSALES extends ISC_ADMIN_BASE
	{
		public function HandleToDo($Do)
		{
			$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->LoadLangFile('sucursales');
			switch (isc_strtolower($Do)) {
				case "saveeditedsucursal":
				{
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Brands)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Sucursales') => "index.php?ToDo=viewSucursales");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SaveEditedSucursales();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}

					break;
				}
				case "editsucursal":
				{
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Brands)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Sucursales') => "index.php?ToDo=viewSucursales", GetLang('EditSucursal') => "index.php?ToDo=editSucursal");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->EditSucursal();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}

					break;
				}
				case "savenewsucursales":
				{
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->SaveNewSucursales();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}

					break;
				}
				case "addsucursal":
				{
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Add_Brands)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Sucursales') => "index.php?ToDo=viewSucursales", GetLang('AddSucursales') => "index.php?ToDo=addSucursal");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->AddSucursales();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}

					break;
				}
				case "deletesucursales":
				{
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Delete_Brands)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Sucursales') => "index.php?ToDo=viewSucursales");

						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						$this->DeleteSucursales();
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						die();
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}

					break;
				}
				default:
				{					
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Brands)) {
						$GLOBALS['BreadcrumEntries'] = array(GetLang('Home') => "index.php", GetLang('Sucursales') => "index.php?ToDo=viewSucursales");

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintHeader();
						}

						$this->ManageSucursales();

						if(!isset($_REQUEST['ajax'])) {
							$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->PrintFooter();
						}
					} else {
						$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
					}
				}
			}
		}

		public function _GetSucursalList(&$Query, $Start, $SortField, $SortOrder, &$NumResults)
		{
			// Return an array containing details about sucursales.
			// Takes into account search too.

			$Query = trim($Query);

			$query = "SELECT s.*, e.statename FROM [|PREFIX|]sucursales s INNER JOIN isc_country_states as e ON e.stateid = s.state_id";

			$countQuery = "SELECT COUNT(*) FROM [|PREFIX|]sucursales s";

			$queryWhere = ' WHERE 1=1 ';
			if ($Query != "") {
				$queryWhere .= " AND s.sucursalname LIKE '%".$GLOBALS['ISC_CLASS_DB']->Quote($Query)."%'";
			}

			$query .= $queryWhere;
			$countQuery .= $queryWhere;

			$result = $GLOBALS['ISC_CLASS_DB']->Query($countQuery);
			$NumResults = $GLOBALS['ISC_CLASS_DB']->FetchOne($result);

			if($NumResults > 0) {
				$query .= " ORDER BY ".$SortField." ".$SortOrder;

				// Add the limit
				$query .= $GLOBALS["ISC_CLASS_DB"]->AddLimit($Start, ISC_SUCURSALES_PER_PAGE);
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);				
				
				return $result;

			}
			else {
				return false;
			}
		}

		public function ManageSucursalesGrid(&$numSucursales)
		{
			// Show a list of news in a table
			$page = 0;
			$start = 0;
			$numSucursales = 0;
			$numPages = 0;
			$GLOBALS['SucursalGrid'] = "";
			$GLOBALS['Nav'] = "";
			$max = 0;
			$searchURL = '';

			if (isset($_GET['searchQuery'])) {
				$query = $_GET['searchQuery'];
				$GLOBALS['Query'] = isc_html_escape($query);
				$searchURL .'searchQuery='.urlencode($query);
			} else {
				$query = "";
				$GLOBALS['Query'] = "";
			}

			if (isset($_GET['sortOrder']) && $_GET['sortOrder'] == 'desc') {
				$sortOrder = 'desc';
			} else {
				$sortOrder = "asc";
			}

			$sortLinks = array(
				"Sucursal" => "s.sucursalname",
				"Direcciones" => "sucursaladdress",
				"State" => "e.statename"
			);

			if (isset($_GET['sortField']) && in_array($_GET['sortField'], $sortLinks)) {
				$sortField = $_GET['sortField'];
				SaveDefaultSortField("ManageSucursales", $_REQUEST['sortField'], $sortOrder);
			}
			else {
				list($sortField, $sortOrder) = GetDefaultSortField("ManageSucursales", "s.sucursalname", $sortOrder);
			}

			if (isset($_GET['page'])) {
				$page = (int)$_GET['page'];
			}
			else {
				$page = 1;
			}

			$sortURL = sprintf("&sortField=%s&sortOrder=%s", $sortField, $sortOrder);
			$GLOBALS['SortURL'] = $sortURL;

			// Limit the number of sucursales returned
			if ($page == 1) {
				$start = 1;
			}
			else {
				$start = ($page * ISC_SUCURSALES_PER_PAGE) - (ISC_SUCURSALES_PER_PAGE-1);
			}

			$start = $start-1;

			// Get the results for the query
			$sucursalResult = $this->_GetSucursalList($query, $start, $sortField, $sortOrder, $numSucursales);
			$numPages = ceil($numSucursales / ISC_SUCURSALES_PER_PAGE);

			// Workout the paging navigation
			if($numSucursales > ISC_SUCURSALES_PER_PAGE) {
				$GLOBALS['Nav'] = sprintf("(%s %d of %d) &nbsp;&nbsp;&nbsp;", GetLang('Page'), $page, $numPages);

				$GLOBALS['Nav'] .= BuildPagination($numSucursales, ISC_SUCURSALES_PER_PAGE, $page, sprintf("index.php?ToDo=viewSucursales%s", $sortURL));
			}
			else {
				$GLOBALS['Nav'] = "";
			}

			$GLOBALS['SearchQuery'] = $query;
			$GLOBALS['SortField'] = $sortField;
			$GLOBALS['SortOrder'] = $sortOrder;

			BuildAdminSortingLinks($sortLinks, "index.php?ToDo=viewSucursales&amp;".$searchURL."&amp;page=".$page, $sortField, $sortOrder);


			// Workout the maximum size of the array
			$max = $start + ISC_SUCURSALES_PER_PAGE;

			if ($max > count($sucursalResult)) {
				$max = count($sucursalResult);
			}

			if($numSucursales > 0) {
				// Display the news
				while ($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($sucursalResult)) {
					$GLOBALS['SucursalId'] = (int) $row['sucursalid'];
					$GLOBALS['SucursalName'] = isc_html_escape($row['sucursalname']);
					$GLOBALS['Direcciones'] = isc_html_escape($row['sucursaladdress']);
					$GLOBALS['State'] = isc_html_escape($row['statename']);
					
					// Workout the edit link -- do they have permission to do so?
					if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Edit_Brands)) {
						$GLOBALS['EditSucursalLink'] = sprintf("<a title='%s' class='Action' href='index.php?ToDo=editSucursal&amp;sucursalId=%d'>%s</a>", GetLang('SucursalEdit'), $row['sucursalid'], GetLang('Edit'));
					} else {
						$GLOBALS['EditNewsLink'] = sprintf("<a class='Action' disabled>%s</a>", GetLang('Edit'));
					}

					$GLOBALS['SucursalGrid'] .= $this->template->render('sucursales.manage.row.tpl');
				}
				return $this->template->render('sucursales.manage.grid.tpl');
			}
		}

		public function ManageSucursales($MsgDesc = "", $MsgStatus = "")
		{
			// Fetch any results, place them in the data grid
			$numSucursales = 0;
			$GLOBALS['SucursalesDataGrid'] = $this->ManageSucursalesGrid($numSucursales);

			// Was this an ajax based sort? Return the table now
			if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == 1) {
				echo $GLOBALS['SucursalesDataGrid'];
				return;
			}

			if ($MsgDesc != "") {
				$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
			}

			if (isset($_GET['searchQuery'])) {
				$GLOBALS['ClearSearchLink'] = '<a id="SearchClearButton" href="index.php?ToDo=viewSucursales">'.GetLang('ClearResults').'</a>';
			} else {
				$GLOBALS['ClearSearchLink'] = '';
			}
			
			$GLOBALS['SucursalIntro'] = GetLang('ManageSucursalesIntro');

			// Do we need to disable the delete button?
			if (!$GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Delete_Brands) || $numSucursales == 0) {
				$GLOBALS['DisableDelete'] = "DISABLED";
			}

			// No results
			if($numSucursales == 0) {
				$GLOBALS['DisplayGrid'] = "none";
				if(count($_GET) > 1) {
					if ($MsgDesc == "") {
						$GLOBALS['Message'] = MessageBox(GetLang('NoSucursalResults'), MSG_ERROR);
					}
				}
				else {
					$GLOBALS['DisplaySearch'] = "none";
					$GLOBALS['Message'] = MessageBox(GetLang('NoSucursales'), MSG_SUCCESS);
				}
			}
				
			$this->template->display('sucursales.manage.tpl');
		}

		public function DeleteSucursales()
		{
			if (isset($_POST['sucursales'])) {

				$sucursalids = implode("','", $GLOBALS['ISC_CLASS_DB']->Quote($_POST['sucursales']));

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction(count($_POST['sucursales']));

				// Delete the sucursales
				$query = sprintf("delete from [|PREFIX|]sucursales where sucursalid in ('%s')", $sucursalids);
				$GLOBALS["ISC_CLASS_DB"]->Query($query);

				// Delete the sucursal associations
				$updatedProducts = array(
					"prodsucursalid" => 0
				);

				// Delete the search record
				$GLOBALS["ISC_CLASS_DB"]->DeleteQuery("sucursal_search", "WHERE sucursalid IN('" . $sucursalids . "')");

				$GLOBALS['ISC_CLASS_DB']->UpdateQuery("products", $updatedProducts, "prodsucursalid IN ('".$sucursalids."')");
				$err = $GLOBALS["ISC_CLASS_DB"]->Error();
				if ($err != "") {
					$this->ManageSucursales($err, MSG_ERROR);
				} else {
					$this->ManageSucursales(GetLang('SucursalesDeletedSuccessfully'), MSG_SUCCESS);
				}
			} else {
				if ($GLOBALS["ISC_CLASS_ADMIN_AUTH"]->HasPermission(AUTH_Manage_Brands)) {
					$this->ManageSucursales();
				} else {
					$GLOBALS['ISC_CLASS_ADMIN_ENGINE']->DoHomePage(GetLang('Unauthorized'), MSG_ERROR);
				}
			}
		}

		public function AddSucursales()
		{
			$GLOBALS['SucursalTitle'] = GetLang('AddSucursales');
			$GLOBALS['SucursalIntro'] = GetLang('AddSucursalIntro');
			$GLOBALS['CancelMessage'] = GetLang('CancelCreateSucursal');
			$GLOBALS['FormAction'] = "SaveNewSucursales";
			$GLOBALS['StateOptions'] = $GLOBALS['ISC_CLASS_ADMIN_SUCURSALES']->GetStatesAsOptions();
			$this->template->display('sucursal.edit.form.tpl');
		}

		public function GetSucursalesAsOptions($SelectedSucursalId = 0)
		{
			// Return a list of sucursales as options for a select box.
			$output = "";
			$sel = "";
			$query = "SELECT * FROM [|PREFIX|]sucursales ORDER BY sucursalname asc";
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				if($row['sucursalid'] == $SelectedSucursalId) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}

				$output .= sprintf("<option value='%d' %s>%s</option>", $row['sucursalid'], $sel, isc_html_escape($row['sucursalname']));
			}

			return $output;
		}

		public function GetSucursalesAsArray(&$RefArray)
		{
			/*
				Return a list of sucursales as an array. This will be used to check
				if a sucursal already exists. It's more efficient to do one query
				rather than one query per sucursal check.

				$RefArray - An array passed in by reference only
			*/

			$query = "select sucursalname from [|PREFIX|]sucursales";
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result))
				$RefArray[] = isc_strtolower($row['sucursalname']);
		}

		public function SaveNewSucursales()
		{
			$sucursales_added = 0;
			$message = "";
			$current_sucursales = array();
			$this->GetSucursalesAsArray($current_sucursales);

			if(isset($_POST['sucursales'])) {
				$sucursales = $_POST['sucursales'];
				$sucursal_list = explode("\n", $sucursales);

				// Log this action
				$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($sucursal_list);

				// Save the sucursales to the database
				foreach($sucursal_list as $sucursal) {
					$sucursal = trim($sucursal);
					if(!in_array(isc_strtolower($sucursal), $current_sucursales) && trim($sucursal) != "") {
						$newSucursal = array(
							"sucursalname" => $sucursal,
							"sucursalpagetitle" => "",
							"sucursalmetakeywords" => "",
							"sucursalmetadesc" => "",
							"sucursalesearchkeywords" => ""
						);

						$newSucursalId = $GLOBALS['ISC_CLASS_DB']->InsertQuery("sucursales", $newSucursal);

						if (isId($newSucursalId)) {

							// Save to our sucursal search table
							$searchData = array(
								"sucursalid" => $newSucursalId,
								"sucursalname" => $sucursal,
								"sucursalpagetitle" => "",
								"sucursalesearchkeywords" => ""
							);

							$GLOBALS['ISC_CLASS_DB']->InsertQuery("sucursal_search", $searchData);

							// Save the words to the sucursal_words table for search spelling suggestions
							Store_SearchSuggestion::manageSuggestedWordDatabase("sucursal", $newSucursalId, $sucursal);
						}

						++$sucursales_added;
					}
				}

				// Check for an error message from the database
				if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {
					// No error
					if($sucursales_added == 1) {
						$message = GetLang('OneSucursalAddedSuccessfully');
					}
					else {
						$message = sprintf(GetLang('MultiSucursalesAddedSuccessfully'), $sucursales_added);
					}

					$this->ManageSucursales($message, MSG_SUCCESS);
				}
				else {
					// Something went wrong
					$message = sprintf(GetLang('SucursalAddError'), $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg());
					$this->ManageSucursales($message, MSG_ERROR);
				}
			}
			else {
				ob_end_clean();
				header("Location: index.php?ToDo=viewSucursales");
				die();
			}
		}

		public function EditSucursal($MsgDesc = "", $MsgStatus = "")
		{
			if(isset($_GET['sucursalId'])) {
				if ($MsgDesc != "") {
					$GLOBALS['Message'] = MessageBox($MsgDesc, $MsgStatus);
				}

				$sucursalId = (int)$_GET['sucursalId'];
				$query = sprintf("select * from [|PREFIX|]sucursales where sucursalid='%d'", $GLOBALS['ISC_CLASS_DB']->Quote($sucursalId));
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

				if($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
					$GLOBALS['SucursalId'] = $row['sucursalid'];
					$GLOBALS['SucursalName'] = isc_html_escape($row['sucursalname']);
					$GLOBALS['SucursalAddress'] = isc_html_escape($row['sucursaladdress']);
					$GLOBALS['StateId'] = $row['state_id'];
					
					$GLOBALS['SucursalTitle'] = GetLang('EditSucursal');
					$GLOBALS['SucursalIntro'] = GetLang('EditSucursalIntro');
					$GLOBALS['CancelMessage'] = GetLang('CancelEditSucursal');
					$GLOBALS['FormAction'] = "SaveEditedSucursal";					
					
					$GLOBALS['StateOptions'] = $GLOBALS['ISC_CLASS_ADMIN_SUCURSALES']->GetStatesAsOptions($row['state_id']);			
					
					$this->template->display('sucursal.edit.form.tpl');
				}
				else {
					ob_end_clean();
					header("Location: index.php?ToDo=viewSucursales");
					die();
				}
			}
			else {
				ob_end_clean();
				header("Location: index.php?ToDo=viewSucursales");
				die();
			}
			
		}

		public function SaveEditedSucursales()
		{
			if(isset($_POST['sucursalName'])) {
				$sucursalId = (int)$_POST['sucursalId'];
				$oldSucursalName = $_POST['oldSucursalName'];
				$sucursalName = $_POST['sucursalName'];
				$sucursalMap = $_POST['sucursalMap'];
				$sucursalInfo = $_POST['sucursalInfo'];
				$sucursalLink = $_POST['sucursalLink'];
				$sucursalPageTitle = $_POST['sucursalPageTitle'];
				$sucursalMetaKeywords = $_POST['sucursalMetaKeywords'];
				$sucursalMetaDesc = $_POST['sucursalMetaDesc'];
				$sucursalSearchKeywords = $_POST['sucursalSearchKeywords'];

				// Make sure the sucursal doesn't already exist
				$query = sprintf("select count(sucursalid) as num from [|PREFIX|]sucursales where sucursalname='%s' and sucursalname !='%s'", $GLOBALS['ISC_CLASS_DB']->Quote($sucursalName), $GLOBALS['ISC_CLASS_DB']->Quote($oldSucursalName));
				$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);
				$row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result);

				if($row['num'] == 0) {
					// Log this action
					$GLOBALS['ISC_CLASS_LOG']->LogAdminAction($_POST['sucursalId'], $_POST['sucursalName']);

					// No duplicates
					$updatedSucursal = array(
						"sucursalname" => $sucursalName,
						"sucursalinfo" => $sucursalInfo,
						"sucursalgmap" => $sucursalMap,
						"sucursallink" => $sucursalLink,
						"sucursalpagetitle" => $sucursalPageTitle,
						"sucursalmetakeywords" => $sucursalMetaKeywords,
						"sucursalmetadesc" => $sucursalMetaDesc,
						"sucursalesearchkeywords" => $sucursalSearchKeywords
					);
					$GLOBALS['ISC_CLASS_DB']->UpdateQuery("sucursales", $updatedSucursal, "sucursalid='".$GLOBALS['ISC_CLASS_DB']->Quote($sucursalId)."'");
					if($GLOBALS["ISC_CLASS_DB"]->GetErrorMsg() == "") {

						// Update our sucursal search table
						$searchData = array(
							"sucursalid" => $sucursalId,
							"sucursalname" => $sucursalName,
							"sucursalpagetitle" => $sucursalPageTitle,
							"sucursalesearchkeywords" => $sucursalSearchKeywords
						);

						$query = "SELECT sucursalesearchid
									FROM [|PREFIX|]sucursal_search
									WHERE sucursalid=" . (int)$sucursalId;

						$searchId = $GLOBALS["ISC_CLASS_DB"]->FetchOne($query);

						if (isId($searchId)) {
							$GLOBALS['ISC_CLASS_DB']->UpdateQuery("sucursal_search", $searchData, "sucursalesearchid = " . (int)$searchId);
						} else {
							$GLOBALS['ISC_CLASS_DB']->InsertQuery("sucursal_search", $searchData);
						}

						// Save the words to the sucursal_words table for search spelling suggestions
						Store_SearchSuggestion::manageSuggestedWordDatabase("sucursal", $sucursalId, $sucursalName);

						if (array_key_exists('delsucursalimagefile', $_POST) && $_POST['delsucursalimagefile']) {
							$this->DelSucursalImage($sucursalId);
							$GLOBALS['ISC_CLASS_DB']->UpdateQuery('sucursales', array('sucursalimagefile' => ''), "sucursalid='" . (int)$sucursalId . "'");
						} else if (array_key_exists('sucursalimagefile', $_FILES) && ($sucursalimagefile = $this->SaveSucursalImage())) {
							$GLOBALS['ISC_CLASS_DB']->UpdateQuery('sucursales', array('sucursalimagefile' => $sucursalimagefile), "sucursalid='" . (int)$sucursalId . "'");
						}

						$this->ManageSucursales(GetLang('SucursalUpdatedSuccessfully'), MSG_SUCCESS);
					}
					else {
						$this->EditSucursal(sprintf(GetLang('UpdateSucursalError'), $GLOBALS["ISC_CLASS_DB"]->GetErrorMsg()), MSG_ERROR);
					}
				}
				else {
					// Duplicate sucursal name, take them back to the 'Edit' page
					$_GET['sucursalId'] = $sucursalId;
					$this->EditSucursal(sprintf(GetLang('DuplicateSucursalName'), $sucursalName), MSG_ERROR);
				}
			}
			else {
				ob_end_clean();
				header("Location: index.php?ToDo=viewSucursales");
				die();
			}
		}

		
		public function GetStatesAsOptions($SelectedStateId = 0)
		{
			// Return a list of brands as options for a select box.
			$output = "";
			$sel = "";
			$query = "SELECT * FROM [|PREFIX|]country_states WHERE statecountry = 10 ORDER BY statename asc";
			$result = $GLOBALS["ISC_CLASS_DB"]->Query($query);

			while($row = $GLOBALS["ISC_CLASS_DB"]->Fetch($result)) {
				if($row['stateid'] == $SelectedStateId) {
					$sel = "selected=\"selected\"";
				}
				else {
					$sel = "";
				}

				$output .= sprintf("<option value='%d' %s>%s</option>", $row['stateid'], $sel, isc_html_escape($row['statename']));
			}

			return $output;
		}	
		
	}