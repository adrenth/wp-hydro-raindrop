<?php
/**
 * Default Hydro Raindrop Meta Box content.
 */

$mfa_required = get_post_meta( $post->ID, Hydro_Raindrop_Helper::POST_META_MFA_REQUIRED, true );

?>
<table class="form-table">
	<tr>
		<td>
			<label>
				<input name="<?php echo esc_attr( Hydro_Raindrop_Helper::POST_META_MFA_REQUIRED ); ?>"
					type="checkbox"
					value="1"
					<?php if ( $mfa_required ) : ?>
						checked
					<?php endif; ?>
				>
				Requires MFA to view
			</label>
		</td>
	</tr>
</table>
