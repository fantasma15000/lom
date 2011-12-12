{% import 'macros/util.tpl' as util %}

	<form action="index.php?ToDo=saveUpdatedCheckoutSettings" name="frmCheckoutSettings" id="frmCheckoutSettings" method="post" onsubmit="return ValidateForm(CheckCheckoutSettingsForm)">
		<div class="BodyContainer">
		<table cellSpacing="0" cellPadding="0" width="100%" style="margin-left: 4px; margin-top: 8px;">
		<tr>
			<td class="Heading1">{% lang 'CheckoutSettings' %}</td>
		</tr>
		<tr>
			<td class="Intro">
				<p>{% lang 'CheckoutSettingsIntro' %}</p>
				<div id="CheckoutStatus">
					{{ Message|safe }}
				</div>
				<p class="TopButtons">
					<input type="submit" value="{% lang 'Save' %}" class="FormButton SaveButton" />
					<input type="reset" value="{% lang 'Cancel' %}" class="FormButton CancelButton" onclick="ConfirmCancel()" />
				</p>
			</td>
		</tr>
		<tr>
			<td>
				<ul id="tabnav">
					<li><a href="#" class="active" id="tab0" onclick="ShowTab(0)">{% lang 'GeneralSettings' %}</a></li>
					{{ CheckoutTabs|safe }}
				</ul>
			</td>
		</tr>
		<tr>
			<td>
				<input id="currentTab" name="currentTab" value="0" type="hidden">
				<div id="div0" style="padding-top: 10px;">
					<table width="100%" class="Panel">
						<tr>
							<td class="Heading2" colspan="2">{% lang 'CheckoutSettings' %}</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="storename">{% lang 'CheckoutMethods' %}:</label>
							</td>
							<td>
								<div id="BuiltInGatewayOption" style="{{ HideBuiltInGateway|safe }}">
									<div>
										<label><input type="radio" id="CheckoutMethodBuiltIn" name="builtInGateway" value="1" {{ UseBuiltInGatewayChecked|safe }} /> {{ UseBuiltInGateway|safe }}</label>
									</div>
									<div class="builtInGateway builtInGateway_1">
										<img src="images/nodejoin.gif" alt="" class="FloatLeft" />
										<div class="BuiltInGatewayForm">
											{{ BuiltInGatewayErrors|safe }}
											<div class="BuiltInCheckoutIntro">
												{{ BuiltInGatewayIntro|safe }}
											</div>
											{{ BuiltInGatewayProperties|safe }}
										</div>
									</div>

									<div>
										<label><input type="radio" id="CheckoutMethodCustom" name="builtInGateway" value="0" {{ UseCustomGatewayChecked|safe }} /> {% lang 'UseCustomGateway' %}</label>
									</div>
									<img src="images/nodejoin.gif" alt="" class="FloatLeft builtInGateway builtInGateway_0" />
								</div>
								<div class="builtInGateway builtInGateway_0">
									<select size="20" multiple="multiple" name="checkoutproviders[]" id="checkoutproviders" class="Field300 ISSelectReplacement {{ CheckoutProviderClass|safe }}">
										{{ CheckoutProviders|safe }}
									</select>
									<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '{% lang 'CheckoutMethods' %}', '{% lang 'CheckoutMethodsHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display:none" id="d1"></div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">{% lang 'CheckoutType' %}:</td>
							<td>
								<select name="CheckoutType" class="Field300">
									<option value="single" {{ CheckoutTypeSingleSelected|safe }}>{% lang 'CheckoutTypeSingle' %}</option>
									<option value="multipage" {{ CheckoutTypeMultiSelected|safe }}>{% lang 'CheckoutTypeMulti' %}</option>
								</select>
								<img onmouseout="HideHelp('chktype');" onmouseover="ShowHelp('chktype', '{% lang 'CheckoutType' %}', '{% lang 'CheckoutTypeHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="chktype"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">{% lang 'EnableGuestCheckout' %}?</td>
							<td>
								<label><input type="checkbox" name="GuestCheckoutEnabled" value="1" onclick="$('.GuestCheckoutEnabledToggle').toggle();" {{ GuestCheckoutChecked|safe }} /> {% lang 'YesEnableGuestCheckout' %}</label>
								<img onmouseout="HideHelp('guestcheckout');" onmouseover="ShowHelp('guestcheckout', '{% lang 'EnableGuestCheckout' %}?', '{% lang 'EnableGuestCheckoutHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="guestcheckout"></div>
								<div style="{{ HideGuestCheckoutCreateAccounts|safe }}" class="GuestCheckoutEnabledToggle">
									<img src="images/nodejoin.gif" alt="" />
									<label><input type="checkbox" name="GuestCheckoutCreateAccounts" value="1" {{ GuestCheckoutCreateAccountsCheck|safe }} /> {% lang 'YesAutoCreateGuestAccounts' %}</label>
									<img onmouseout="HideHelp('guestcheckout2');" onmouseover="ShowHelp('guestcheckout2', '{% lang 'AutoCreateGuestAccounts' %}?', '{% lang 'AutoCreateGuestAccountsHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
									<div style="display:none" id="guestcheckout2"></div>
								</div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="EnableOrderComments">{% lang 'EnableOrderComments' %}?</label>
							</td>
							<td>
								<input type="checkbox" name="EnableOrderComments" id="EnableOrderComments" value="1" {{ IsEnableOrderComments|safe }} /> <label for="EnableOrderComments">{% lang 'YesEnableOrderComments' %}</label>
								<img onmouseout="HideHelp('OrderCommentsHelp');" onmouseover="ShowHelp('OrderCommentsHelp', '{% lang 'EnableOrderComments' %}?', '{% lang 'EnableOrderCommentsHelp' %}')" src="images/help.gif" width="24" height="16" border="0" />
								<div style="display:none" id="OrderCommentsHelp"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="EnableOrderComments">{% lang 'EnableOrderTermsAndConditions' %}?</label>
							</td>
							<td>
								<input onclick="$('.OrderTermsAndConditions').toggle();" type="checkbox" name="EnableOrderTermsAndConditions" id="EnableOrderTermsAndConditions" value="1" {{ IsEnableOrderTermsAndConditions|safe }} /> <label for="EnableOrderTermsAndConditions">{% lang 'YesEnableOrderTermsAndConditions' %}</label>
								<img onmouseout="HideHelp('EnableOrderTermsAndConditionsHelp');" onmouseover="ShowHelp('EnableOrderTermsAndConditionsHelp', '{% lang 'EnableOrderTermsAndConditions' %}?', '{% lang 'EnableOrderTermsAndConditionsHelp' %}')" src="images/help.gif" width="24" height="16" border="0" />
								<div style="display:none" id="EnableOrderTermsAndConditionsHelp"></div>
								<div style="{{ HideOrderTermsAndConditions|safe }}" class="OrderTermsAndConditions">
									<table>
										<tr>
											<td valign="top"><img src="images/nodejoin.gif" alt="" /></td>
											<td>


												<input onclick="ToggleTermsAndConditions('link');" type="radio" name="OrderTermsAndConditionsType" id="TNCLink" class="OrderTermsAndConditionsType" value="link"  {{ IsEnableOrderTermsAndConditionsLink|safe }} />
												<label for="TNCLink">{% lang 'LinkToMyTermsAndConditions' %}:</label>
												<img onmouseout="HideHelp('OrderTermsAndConditionsLinkHelp');" onmouseover="ShowHelp('OrderTermsAndConditionsLinkHelp', '{% lang 'OrderTermsAndConditionsLink' %}?', '{% lang 'OrderTermsAndConditionsLinkHelp' %}')" src="images/help.gif" width="24" height="16" border="0" />
												<div style="display:none" id="OrderTermsAndConditionsLinkHelp"></div>
												<br />
												<input style="margin-left:25px; {{ HideOrderTermsAndConditionsLink|safe }}" class="Field250 OrderTermsAndConditionsLink" name="OrderTermsAndConditionsLink" value="{{ OrderTermsAndConditionsLink|safe }}">

											</td>
										</tr>
										<tr>
											<td valign="top"><img src="images/nodejoin.gif" alt="" /></td>
											<td>

												<input onclick="ToggleTermsAndConditions('textarea');" type="radio" name="OrderTermsAndConditionsType" id="TNCText" class="OrderTermsAndConditionsType" value="textarea"  {{ IsEnableOrderTermsAndConditionsTextarea|safe }} />
												<label for="TNCText">{% lang 'LetMeTypeTermsAndConditions' %}:</label>
												<img onmouseout="HideHelp('OrderTermsAndConditionsTextareaHelp');" onmouseover="ShowHelp('OrderTermsAndConditionsTextareaHelp', '{% lang 'OrderTermsAndConditionsText' %}?', '{% lang 'OrderTermsAndConditionsTextareaHelp' %}')" src="images/help.gif" width="24" height="16" border="0" />
												<div style="display:none" id="OrderTermsAndConditionsTextareaHelp"></div>
												<br />
												<textarea  style="margin-left:25px; {{ HideOrderTermsAndConditionsTextarea|safe }}" class="Field250 OrderTermsAndConditionsTextarea" name="OrderTermsAndConditionsTextarea" rows="5">{{ OrderTermsAndConditions|safe }}</textarea>

											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
						<tr style="{{ HideMultiShipping|safe }}">
							<td class="FieldLabel">
								<label for="MultipleShippingAddresses">{% lang 'EnableMultipleShippingAddresses' %}:</label>
							</td>
							<td class="PanelBottom">
								<input type="checkbox" name="MultipleShippingAddresses" id="MultipleShippingAddresses" value="1" {{ IsMultipleShippingAddressesEnabled|safe }} /> <label for="MultipleShippingAddresses">{% lang 'YesEnableMultipleShippingAddresses' %}</label>
								<img onmouseout="HideHelp('MultipleShippingAddressesHelp');" onmouseover="ShowHelp('MultipleShippingAddressesHelp', '{% lang 'EnableMultipleShippingAddresses' %}?', '{% lang 'EnableMultipleShippingAddressesHelp' %}')" src="images/help.gif" width="24" height="16" border="0" />
								<div style="display:none" id="MultipleShippingAddressesHelp"></div>
							</td>
						</tr>
					</table>
					<table width="100%" class="Panel">
						<tr>
							<td class="Heading2" colspan="2">{% lang 'OrderSettings' %}</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="updateinventory">{% lang 'UpdateProductInventoryWhen' %}:</label>
							</td>
							<td class="PanelBottom">
								<select name="updateinventory" id="updateinventory" class="Field300">
									<option value="1" {{ UpdateInventorySuccessfulSelected|safe }}>{% lang 'UpdateInventorySuccessfulOrder' %}</option>
									<option value="2" {{ UpdateInventoryCompletedSelected|safe }}>{% lang 'UpdateInventoryOrderCompleted' %}</option>
								</select>
								<img onmouseout="HideHelp('ad1');" onmouseover="ShowHelp('ad1', '{% lang 'UpdateProductInventoryWhen' %}', '{% lang 'UpdateProductInventoryWhenHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="ad1"></div>
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="UpdateInventoryOnOrderEdit">{% lang 'UpdateInventoryOnOrderEdit' %}</label>
							</td>
							<td class="PanelBottom">
								<input type="checkbox" name="UpdateInventoryOnOrderEdit" id="UpdateInventoryOnOrderEdit" value="1" {% if config.UpdateInventoryOnOrderEdit %}checked="checked"{% endif %} /> <label for="UpdateInventoryOnOrderEdit">{% lang 'YesUpdateInventoryOnOrderEdit' %}</label>
								{{ util.tooltip('UpdateInventoryOnOrderEdit', 'UpdateInventoryOnOrderEditHelp') }}
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="UpdateInventoryOnOrderDelete">{% lang 'UpdateInventoryOnOrderDelete' %}</label>
							</td>
							<td class="PanelBottom">
								<input type="checkbox" name="UpdateInventoryOnOrderDelete" id="UpdateInventoryOnOrderDelete" value="1" {% if config.UpdateInventoryOnOrderDelete %}checked="checked"{% endif %} /> <label for="UpdateInventoryOnOrderDelete">{% lang 'YesUpdateInventoryOnOrderDelete' %}</label>
								{{ util.tooltip('UpdateInventoryOnOrderDelete', 'UpdateInventoryOnOrderDeleteHelp') }}
							</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="orderstatusemails">{% lang 'EmailOnOrderStatusChange' %}:</label>
							</td>
							<td class="PanelBottom">
								<select name="orderstatusemails[]" id="orderstatusemails" class="Field300 ISSelectReplacement" size="11" multiple="multiple">
									{{ OrderStatusEmailList|safe }}
								</select>
								<img onmouseout="HideHelp('ad2');" onmouseover="ShowHelp('ad2', '{% lang 'EmailOnOrderStatusChange' %}', '{% lang 'EmailOnOrderStatusChangeHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display:none" id="ad2"></div>
							</td>
						</tr>
					</table>
					<table width="100%" class="Panel">
						<tr>
							<td class="Heading2" colspan="2">{% lang 'DigitalProductSettings' %}</td>
						</tr>
						<tr>
							<td class="FieldLabel">
								<label for="orderstatusemails">{% lang 'EnableDigitalHandlingFee' %}?</label>
							</td>
							<td class="PanelBottom">
								<label><input type="checkbox" onclick="$('.DigitalOrderHandling').toggle();" id="EnableDigitalOrderHandlingFee" name="EnableDigitalOrderHandlingFee" {{ DigitalOrderHandlingFeeChecked|safe }} /> {% lang 'YesEnableDigitalHandlingFee' %}</label>
								<img onmouseout="HideHelp('digitalhandling');" onmouseover="ShowHelp('digitalhandling', '{% lang 'EnableDigitalHandlingFee' %}?', '{% lang 'EnableDigitalHandlingFeeHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
								<div style="display: none" id="digitalhandling"></div>
								<div style="{{ HideDigitalOrderHandlingFee|safe }}" class="DigitalOrderHandling">
									<table>
										<tr>
											<td><img src="images/nodejoin.gif" alt="" /></td>
											<td>
												Handling Fee:
												{{ LeftCurrencyToken|safe }}
												<input type="text" name="DigitalOrderHandlingFee" id="DigitalOrderHandlingFee" class="Field50" value="{{ DigitalOrderHandlingFee|safe }}" />
												{{ RightCurrencyToken|safe }}
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					</table>
				</div>
				{{ CheckoutDivs|safe }}
				<table border="0" cellspacing="0" cellpadding="2" width="100%" class="PanelPlain" id="BottomButtons">
					<tr>
						<td width="200" class="FieldLabel">
							&nbsp;
						</td>
						<td>
							<input type="submit" value="{% lang 'Save' %}" class="FormButton SaveButton" />
							<input type="reset" value="{% lang 'Cancel' %}" class="FormButton CancelButton" onclick="ConfirmCancel()" />
						</td>
					</tr>
				</table>
			</td>
		</tr>
		</table>
		</div>
	</form>

	<div id="ViewsMenu" class="DropShadow DropDownMenu" style="display: none; width:200px">
		<ul>
			{{ CheckoutFieldsOptions|safe }}
		</ul>
	</div>

	<script type="text/javascript">

		function ToggleTermsAndConditions(type)
		{
			if(type == 'link') {
				$('.OrderTermsAndConditionsLink').css({display: ''});
				$('.OrderTermsAndConditionsTextarea').css({display: 'none'});
			} else {
				$('.OrderTermsAndConditionsTextarea').css({display: ''});
				$('.OrderTermsAndConditionsLink').css({display: 'none'});
			}
		}

		function checkout_selected(checkout_id) {
			if(checkout_id == 'builtin') {
				if($('input[name=builtInGateway]:checked').val() == 1) {
					return true;
				}
				else {
					return false;
				}
			}

			if(g('checkoutproviders_old')) {
				var cp = g('checkoutproviders_old');
			}
			else {
				var cp = document.getElementById("checkoutproviders");
			}
			for(i = 0; i < cp.options.length; i++) {
				if(cp.options[i].value == checkout_id && cp.options[i].selected) {
					return true;
				}
			}
			return false;
		}

		function ShowTab(T)
		{
			i = 0;
			while (document.getElementById("tab" + i) != null) {
				document.getElementById("div" + i).style.display = "none";
				document.getElementById("tab" + i).className = "";
				i++;
			}

			document.getElementById("div" + T).style.display = "";
			document.getElementById("tab" + T).className = "active";
			document.getElementById("currentTab").value = T;
		}

		function CheckCheckoutSettingsForm()
		{
			if($('input[name=builtInGateway]:checked').val() == 1 && $('.BuiltInGatewayForm .MessageBoxError').length > 0) {
				alert('{% lang 'CannotEnableBuiltInModuleErrors' %}');
				return false;
			}

			if($('#EnableDigitalOrderHandlingFee').attr('checked')) {
				if($('#DigitalOrderHandlingFee').val() == '' || isNaN(priceFormat($('#DigitalOrderHandlingFee').val()))) {
					alert('{% lang 'EnterDigitalOrderHandlingFee' %}');
					$('#DigitalOrderHandlingFee').select();
					return false;
				}
			}

			if($('#EnableOrderTermsAndConditions').attr('checked')) {
				if($('.OrderTermsAndConditionsType:checked').val() == 'link') {
					if($('.OrderTermsAndConditionsLink').val() == '' || $('.OrderTermsAndConditionsLink').val() == 'http://') {
						alert("{% lang 'EnterTermsAndConditionsLink' %}");
						return false;
					}
				} else if($('.OrderTermsAndConditionsType:checked').val() == 'textarea') {
					if($('.OrderTermsAndConditionsTextarea').val() == '') {
						alert("{% lang 'EnterTermsAndConditions' %}");
						return false;
					}
				} else {
					alert("{% lang 'SelectTermsAndConditionsType' %}");
					return false;
				}
			}

			{{ CheckoutJavaScript|safe }}
		}

		function ConfirmCancel() {
			if(confirm('{% lang 'CancelCheckoutMessage' %}')) {
				document.location.href='index.php?ToDo=viewCheckoutSettings';
			}
			else {
				return false;
			}
		}

		function ToggleBuiltInGateway()
		{
			// Built in gateway is disabled
			if($('#BuiltInGatewayOption').css('display') == 'none') {
				return;
			}
			selected = $('input[name=builtInGateway]:checked').val();
			$('.builtInGateway').hide();
			$('.builtInGateway_'+selected).show();
		}

		$(document).ready(function() {
			$('input[name=builtInGateway]').click(function() {
				ToggleBuiltInGateway();
			});
			ToggleBuiltInGateway();

			ShowTab({{ CurrentTab|safe }});

		});

	</script>