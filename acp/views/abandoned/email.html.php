<section class="add_table">
	<h4 class="heading"><i class="fa fa-caret-down"></i><span><?=$CMS->lang['abandoned_emails'];?></span></h4>
	<div>
		<div class="table-responsive" style="overflow-x: initial;">
			<table class="table_cus" width="100%">
				<thead>
					<tr>
						<th><?=$CMS->lang['email_no.'];?></th>
						<th><?=$CMS->lang['email_id'];?></th>
						<th><?=$CMS->lang['email_title'];?></th>
						<th><?=$CMS->lang['email_date'];?></th>
					</tr>
				</thead>
				<tbody>
					<?
					if( is_array($tpl->list_mail) )
					{
						$i=0;
						foreach( $tpl->list_mail as $email ) 
						{
							if( $email )
							{
								$i++;
								$email = $CMS->email->get_info($email);
					?>
					<tr>
						<td>#<?=$i;?></td>
						<td><a href="<?=$CMS->vars['root_domain'];?>/?site=email&act=show&id=<?=$email['email_id'];?>">#<?=$email['email_id'];?></a></td>
                        <td><?=$email['email_title'];?></td>
                        <td><?=$CMS->class->date->date_format($email['email_time'], 1);?></td>
					</tr>
					<?}}}?>
				</tbody>
			</table>
		</div>
	</div>
</section>