<?php
use \core\ezy;

if (!defined('IN_ROOT')) exit();

$subscription = new subscription;
$subscription->autorun();

class subscription
{
	public $html;

	public function autorun()
	{
		 
		global $CMS, $DB, $member;
		
		$CMS->class->language->load("subscription");

		
		switch ($CMS->input['act'])
		{
			case 'upgrade_package':
				$this->upgrade_package();
			break;

			case 'select_package':
				$this->select_package();
			break;

			case 'change_cycle_package':
				$this->change_cycle_package();
			break;

			case 'payment':
				$this->payment();
			break;

			case 'payment_success':
				$this->payment_success();
			break;

			case 'payment_cancel':
				$this->payment_cancel();
			break;

			case 'payment_result':
				$this->payment_result();
			break;

            case 'cancel_package':
                $this->cancel_package();
                break;

			default:
				$this->default_page();
			break;
		}
	}

	public function default_page()
	{ 
		global $CMS, $tpl;

		$CMS->core->page_title = $CMS->lang['subscription_title'];

        $CMS->subscription->per_page=10;
        $tpl->data = $CMS->subscription->listing();

        // Output data
        $CMS->output .= ezy::html("default");
	}

	public function upgrade_package()
	{ 
		global $CMS, $member, $tpl;

		$CMS->core->page_title = $CMS->lang['upgrade_title'];

		// Delete cache
  		$CMS->class->cache->mdelete("user");

		// list package
        $tpl->packageList = $CMS->subscription->getPackageList();

        // site infomation
        $tpl->siteInfo = $CMS->vars['siteInfo'];

        // Output data
        $CMS->output .= ezy::html("package");
	}

	public function select_package()
	{
		global $CMS, $DB, $member;

		if(!$CMS->subscription->checkValidPackage($CMS->input['package_name'], $CMS->vars['siteInfo']['site_license_package']))
        {
            $_SESSION['msg'] = $CMS->lang['invalid_package'];
            $CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=upgrade_package');
        }

		$CMS->core->page_title = $CMS->lang['upgrade_title'];

		// list package
		$package_list = $CMS->subscription->getPackageList();

		$package = array();
		foreach ( $package_list as $package_details )
		{
			if ( $package_details['package_name'] == $CMS->input['package_name'] )
			{
				$package = $package_details;
				$package['payment_cycle'] = explode(',',$package['payment_cycle']);
				break;
			}
		}

		if ( !$package )
		{
			$CMS->global->redirect($CMS->vars['root_domain'] . '?site=subscription&act=upgrade_package');
		}

		// cart
		$_SESSION['cart'] = array(
				'key' => 'SUB'.$CMS->vars['siteInfo']['site_id'] . '_' . time(),
				'info' => array(
					'package_name' => $package['package_name'],
					'package_price' => $package['package_price'],
					'package_cycle' => $package['payment_cycle']['0'],
					'package_total' => $package['package_price'],
					'payment_cycle' => $package['payment_cycle']
				),
				'total_amount' => $package['package_price']
			);

		$_SESSION['payment']['step'] = 'cart';
		$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
	}

	public function change_cycle_package()
	{
		global $CMS;

		$package_cycle = intval($CMS->input['package_cycle']);
		if ( $_SESSION['cart'] AND in_array($package_cycle, $_SESSION['cart']['info']['payment_cycle']) )
		{
			$package_total = $package_cycle * $_SESSION['cart']['info']['package_price'];

			$_SESSION['cart']['info']['package_cycle'] = $package_cycle;
			$_SESSION['cart']['info']['package_total'] = $package_total;
			$_SESSION['cart']['total_amount'] = $package_total;
		}

		print $CMS->class->input->currency($_SESSION['cart']['total_amount']);
		exit;
	}

	public function payment()
	{
		global $CMS, $DB, $member;

		if ( $CMS->input['back_to_cart'] )
		{
			$_SESSION['payment']['step'] = 'cart';
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
		}
		
		switch ( $_SESSION['payment']['step'] )
		{
			case 'payment':
				$this->payment_do();
				break;

			default:
				$this->payment_cart();
			break;
		}
	}

	public function payment_cart()
	{
		global $CMS, $tpl;

		$CMS->core->page_title = $CMS->lang['upgrade_title'];

		if ( !$_SESSION['cart'] )
		{
			$CMS->global->redirect($CMS->vars['root_domain'] . '?site=subscription&act=upgrade_package');
		}

		if ( $CMS->input['payment_method_do'] == 1 AND $_SESSION['payment']['step'] == 'cart' )
		{
			$_SESSION['payment']['step'] = 'payment';
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
		}

        $tpl->cart = $_SESSION['cart'];

		$tpl->cart_actived = 'active';

		$CMS->output .= ezy::html('cart');
	}

	public function payment_do()
	{
		global $CMS, $tpl;

        $tpl->cart_actived = 'active';
        $tpl->payment_actived = 'active';

		if ( $CMS->input['payment_do'] )
		{
			$CMS->subscription->payment();
		}

		$tpl->cart = $_SESSION['cart'];

		$payer = [
		    'name' => isset($CMS->input['payer_name']) ? $CMS->input['payer_name'] : $CMS->vars['siteInfo']['fullname'],
		    'phone' => isset($CMS->input['payer_phone']) ? $CMS->input['payer_phone'] : $CMS->vars['siteInfo']['phone'],
		    'email' => isset($CMS->input['payer_email']) ? $CMS->input['payer_email'] : $CMS->vars['siteInfo']['email'],
		    'address' => isset($CMS->input['payer_address']) ? $CMS->input['payer_address'] : $CMS->vars['siteInfo']['address'],
        ];

		$tpl->payer = $payer;

        $CMS->output .= ezy::html('payment');
	}

	public function payment_success()
	{
		global $CMS;

		if ( !$CMS->subscription->payment_success() )
		{
			$CMS->global->redirect($CMS->vars['root_domain'] . '/?site=subscription&act=payment');
		}
	}

	public function payment_cancel()
	{
		global $CMS;

		$CMS->subscription->payment_cancel();
	}

	public function payment_result()
	{
		global $CMS, $tpl;

		if ( ! $_SESSION['payment_result'] )
		{
			$CMS->global->redirect($CMS->vars['root_domain']);
		}

        $tpl->cart_actived = 'active';
        $tpl->payment_actived = 'active';
        $tpl->confirm_actived = 'active';

		$tpl->payment_result = $_SESSION['payment_result'];
        $CMS->output .= ezy::html('result');
		$_SESSION['payment_result'] = array();
	}

	public function cancel_package()
    {
        global $CMS;

        $CMS->subscription->cancelPackage();

        $CMS->global->redirect($CMS->vars['root_domain'].'/?site=subscription&act=upgrade_package');
    }
}