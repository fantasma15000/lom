<tr class="GridRow" onmouseover="this.className='GridRowOver'" onmouseout="this.className='GridRow'">
	<td align="center" style="width:25px">
		<input type="checkbox" name="sucursales[]" value="{{ SucursalId|safe }}">
	</td>
	<td align="center" style="width:18px;">
		<img src='images/brand.gif' width="15" height="15">
	</td>
	<td class="{{ SortedFieldSucursalClass|safe }}">
		{{ SucursalName|safe }}
	</td>
	<td class="{{ SortedFieldDireccionesClass|safe }}">
		{{ Direcciones|safe }}
	</td>
	<td class="{{ SortedFieldStateClass|safe }}">
		{{ State|safe }}
	</td>
	<td>
		{{ EditSucursalLink|safe }}
	</td>
</tr>