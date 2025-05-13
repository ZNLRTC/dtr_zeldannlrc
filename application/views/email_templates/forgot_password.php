<table class="w-100">
  <tr>
    <td class="">
      <table class="">
        <tr>
          <td>
        

            <p>Hi <?= ucwords($name) ?></p>

            <p>You have requested for password change with your account.</p>

             <p>Click this <a href="<?= base_url() . 'reset_password?id='.$user_id.'&token='.urlencode(sha1((string) $date_updated)) ?>" target="_blank">link</a> to set a new password.</p>

            <p><b>Zeldan Nordic Languages Review Center</b></p>

          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>