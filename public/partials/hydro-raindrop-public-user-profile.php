<?php
	/*
	 * Hydro Raindrop 2FA user profile form extension.
	 */
?>
<h2>Hydro Raindrop 2FA</h2>

<p>Your account <strong>does not</strong> have Raindrop 2FA enabled.</p>

<table class="form-table hydro">
	<tr>
		<th>
			<label for="hydro_id">HydroID</label>
		</th>
		<td>
			<input type="text"
					name="hydro_id"
					id="hydro_id"
					value="123456"
					class="code"
					size="7"/>
			<button class="button button-secondary" id="hydro_id_link" type="button">Link</button>
			<p class="description">Enter your HydroID, visible in the Hydro mobile app, and press "Link".</p>
		</td>
	</tr>
</table>
