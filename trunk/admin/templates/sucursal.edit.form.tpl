<form enctype="multipart/form-data" action="index.php?ToDo={{ FormAction|safe }}" onSubmit="return ValidateForm(CheckForm)" name="frmAddSucursal" method="post">
<input type="hidden" name="sucursalId" value="{{ SucursalId|safe }}">
<input type="hidden" name="oldSucursalName" value="{{ SucursalName|safe }}">
<div class="BodyContainer">
	<table class="OuterPanel">
		<tr>
			<td class="Heading1">{{ SucursalTitle|safe }}</td>
		</tr>
		<tr>
		<td class="Intro">
			<p>{{ SucursalIntro|safe }}</p>
			{{ Message|safe }}

		</td>
	</tr>

	<tr>
		<td>
			<div>
				<input type="submit" name="SubmitButton1" value="{% lang 'Save' %}" class="FormButton">
				<input type="button" name="CancelButton1" value="{% lang 'Cancel' %}" class="FormButton" onclick="ConfirmCancel()"><br /><img src="images/blank.gif" width="1" height="10" />
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<table class="Panel">
				<tr>
					<td class="Heading2" colspan=2>{% lang 'SucursalDetails' %}</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;{% lang 'SucursalName' %}:
					</td>
					<td>
						<input type="text" name="sucursalName" id="sucursalName" class="Field400" value="{{ SucursalName|safe }}">
					</td>
				</tr>			
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;{% lang 'SucursalAddress' %}:
					</td>
					<td>
						<input type="text" name="sucursalAddress" id="sucursalAddress" class="Field400" value="{{ SucursalAddress|safe }}">
					</td>
				</tr>
				<tr>
					<td class="FieldLabel">
						<span class="Required">*</span>&nbsp;{% lang 'SucursalState' %}:
					</td>
					<td>
						<select name="stateId" id="stateId" class="Field200">
							<option value="">{% lang 'SucursalStateOpt' %}</option>
							{{ StateOptions|safe }}
						</select>						
					</td>
				</tr>				
			</table>

			<table class="Panel">


				<tr>


					<td class="FieldLabel">&nbsp;</td>


					<td>
						<input type="submit" name="SubmitButton2" value="{% lang 'Save' %}" class="FormButton">
						<input type="button" name="CancelButton2" value="{% lang 'Cancel' %}" class="FormButton" onclick="ConfirmCancel()">
					</td>
				</tr>
				<tr><td class="Gap"></td></tr>
			</table>
		</td>
	</tr>
</table>
</div>
</form>

<script type="text/javascript">

	function CheckForm() {
		var sucursales = document.getElementById("sucursalName");
		var bimg = document.getElementById("sucursalimagefile");

		if(sucursales.value == "") {
			alert("{% lang 'EnterSucursal' %}");
			sucursales.focus();
			return false;
		}

		if(bimg.value != "") {
			// Make sure it has a valid extension
			img = bimg.value.split(".");
			ext = img[img.length-1].toLowerCase();

			if(ext != "jpg" && ext != "png" && ext != "gif") {
				alert("{% lang 'ChooseValidImage' %}");
				bimg.focus();
				bimg.select();
				return false;
			}
		}

		return true;
	}

	function ConfirmCancel()
	{
		if(confirm('{{ CancelMessage|safe }}'))
			document.location.href='index.php?ToDo=viewSucursals';
		else
			return false;
	}

</script>
