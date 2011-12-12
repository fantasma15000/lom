
<form action="index.php?ToDo={{ FormAction|safe }}" onSubmit="return ValidateForm(CheckForm);" name="frmAddSucursal" method="post">
{{ hiddenFields|safe }}
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
					<input type="button" name="CancelButton1" value="{% lang 'Cancel' %}" class="FormButton" onclick="ConfirmCancel()"><br /><img src="images/blank.gif" width="1" height="10" /></div>
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
							<span class="Required">*</span>&nbsp;{% lang 'SucursalNames' %}:
						</td>
						<td>
							<textarea name="sucursales" id="sucursales" class="Field250" rows="5" value=""></textarea>
							<img onmouseout="HideHelp('d1');" onmouseover="ShowHelp('d1', '{% lang 'SucursalNames' %}', '{% lang 'SucursalNamesHelp' %}')" src="images/help.gif" width="24" height="16" border="0">
							<div style="display:none" id="d1"></div>

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
		var sucursales = document.getElementById("sucursales");

		if(sucursales.value == "") {
			alert("{% lang 'EnterSucursals' %}");
			sucursales.focus();
			return false;
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
