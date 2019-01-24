<header class="site-header-report">
	    <div class="container-fluid">
	        <div class="site-header-content">
	            <div class="site-header-content-in" style="margin-left: 0px">
	                <div class="mobile-menu-right-overlay"></div>
	                <div class="site-header-collapsed">
	                    <div class="site-header-collapsed-in">
	                        <div class="dropdown dropdown-typical menu_sales">
	                            <a class="dropdown-toggle" id="dd-header-sales" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon font-icon-cart"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_sales']?></span>
	                            </a>
	 
	                            <div class="dropdown-menu" aria-labelledby="dd-header-sales">
	                                <a class="dropdown-item submenu_sales_date" href="?site=report&act=sales&subact=date"><span class="font-icon font-icon-calend"></span><?=$CMS->lang['title_menu_sales_date']?></a>
                                        
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
	                                <a class="dropdown-item submenu_sales_store" href="?site=report&act=sales&subact=store"><span class="font-icon font-icon-home"></span><?=$CMS->lang['title_menu_sales_store']?></a>
	                                <? } ?>
                                        
                                        <a class="dropdown-item submenu_sales_product" href="?site=report&act=sales&subact=product"><span class="font-icon font-icon-cogwheel"></span><?=$CMS->lang['title_menu_sales_product']?></a>
                                        
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                        <a class="dropdown-item submenu_sales_assets" href="?site=report&act=sales&subact=assets"><span class="font-icon glyphicon glyphicon-barcode"></span><?=$CMS->lang['title_menu_sales_assets']?></a>
	                                <? } ?>
                                        
                                        <a class="dropdown-item submenu_sales_pgroup" href="?site=report&act=sales&subact=pgroup"><span class="font-icon font-icon font-icon-widget"></span><?=$CMS->lang['title_menu_sales_pgroup']?></a>
                                        <a class="dropdown-item submenu_sales_supplier" href="?site=report&act=sales&subact=supplier"><span class="font-icon font-icon font-icon-users"></span><?=$CMS->lang['title_menu_sales_supplier']?></a>
	                            </div>
	                        </div>
	                        
                                <div class="dropdown dropdown-typical menu_order">
	                            <a class="dropdown-toggle" id="dd-header-marketing" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon font-icon-list-square"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_order']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-marketing">
	                                <a class="dropdown-item submenu_order_date" href="?site=report&act=order&subact=date"><span class="font-icon font-icon-calend"></span><?=$CMS->lang['title_menu_order_date']?></a>
	                                <a class="dropdown-item submenu_order_price" href="?site=report&act=order&subact=price"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_order_price']?></a>
	                                <a class="dropdown-item submenu_order_product" href="?site=report&act=order&subact=product"><span class="font-icon font-icon-cogwheel"></span><?=$CMS->lang['title_menu_order_product']?></a>
                                        
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                        <a class="dropdown-item submenu_order_assets" href="?site=report&act=order&subact=assets"><span class="font-icon glyphicon glyphicon-barcode"></span><?=$CMS->lang['title_menu_order_assets']?></a>
	                                <? } ?>
                                        
                                        <a class="dropdown-item submenu_order_status" href="?site=report&act=order&subact=status"><span class="font-icon glyphicon glyphicon-check"></span><?=$CMS->lang['title_menu_order_status']?></a>
	                                <a class="dropdown-item submenu_order_user" href="?site=report&act=order&subact=user"><span class="font-icon font-icon-user"></span><?=$CMS->lang['title_menu_order_user']?></a>
                                    </div>
	                        </div>
                                
                                
	                        <div class="dropdown dropdown-typical menu_product">
	                            <a class="dropdown-toggle" id="dd-header-social" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon font-icon-cogwheel"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_product']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-social">
                                        <a class="dropdown-item submenu_product_bestseller" href="?site=report&act=product&subact=bestseller"><span class="font-icon font-icon-star"></span><?=$CMS->lang['title_menu_product_bestseller']?></a>
	                                <a class="dropdown-item submenu_product_price" href="?site=report&act=product&subact=price"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_product_price']?></a>
	                                <a class="dropdown-item submenu_product_date" href="?site=report&act=product&subact=date"><span class="font-icon font-icon-calend"></span><?=$CMS->lang['title_menu_product_date']?></a>
	                                
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                        <a class="dropdown-item submenu_product_store" href="?site=report&act=product&subact=store"><span class="font-icon font-icon-home"></span><?=$CMS->lang['title_menu_product_store']?></a>
                                        <? } ?>
                                    
                                    </div>
	                        </div>
                                
                                <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                <div class="dropdown dropdown-typical menu_assets">
	                            <a class="dropdown-toggle" id="dd-header-social" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon glyphicon glyphicon-barcode"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_assets']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-social">
                                        <a class="dropdown-item submenu_assets_bestseller" href="?site=report&act=assets&subact=bestseller"><span class="font-icon font-icon-star"></span><?=$CMS->lang['title_menu_assets_bestseller']?></a>
	                                <a class="dropdown-item submenu_assets_price" href="?site=report&act=assets&subact=price"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_assets_price']?></a>
	                                <a class="dropdown-item submenu_assets_date" href="?site=report&act=assets&subact=date"><span class="font-icon font-icon-calend"></span><?=$CMS->lang['title_menu_assets_date']?></a>
	                                <a class="dropdown-item submenu_assets_store" href="?site=report&act=assets&subact=store"><span class="font-icon font-icon-home"></span><?=$CMS->lang['title_menu_assets_store']?></a>
	                                
	                            </div>
	                        </div>
                                <? } ?>
                                
	                        <div class="dropdown dropdown-typical menu_customer">
	                            <a class="dropdown-toggle" id="dd-header-projects" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon font-icon font-icon-users"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_customer']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-projects">
	                                <a class="dropdown-item submenu_customer_overview" href="?site=report&act=customer&subact=overview"><span class="font-icon font-icon-eye"></span><?=$CMS->lang['title_menu_customer_overview']?></a>
	                                <a class="dropdown-item submenu_customer_sales" href="?site=report&act=customer&subact=sales"><span class="font-icon font-icon-cart"></span><?=$CMS->lang['title_menu_customer_sales']?></a>
	                                <a class="dropdown-item submenu_customer_product" href="?site=report&act=customer&subact=product"><span class="font-icon font-icon-cogwheel"></span><?=$CMS->lang['title_menu_customer_product']?></a>
                                        
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                        <a class="dropdown-item submenu_customer_assets" href="?site=report&act=customer&subact=assets"><span class="font-icon glyphicon glyphicon-barcode"></span><?=$CMS->lang['title_menu_customer_assets']?></a>
	                                <a class="dropdown-item submenu_customer_store" href="?site=report&act=customer&subact=store"><span class="font-icon font-icon-home"></span><?=$CMS->lang['title_menu_customer_store']?></a>
                                        <? } ?>
                                    
                                    </div>
	                        </div>
                                
	                        <div class="dropdown dropdown-typical menu_finance">
	                            <a class="dropdown-toggle" id="dd-header-form-builder" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon font-icon-doc"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_finance']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-form-builder">
	                                <a class="dropdown-item submenu_finance_daily" href="?site=report&act=finance&subact=daily"><span class="font-icon glyphicon glyphicon-time"></span><?=$CMS->lang['title_menu_finance_daily']?></a>
	                                <a class="dropdown-item submenu_finance_record" href="?site=report&act=finance&subact=record"><span class="font-icon glyphicon glyphicon-cog"></span><?=$CMS->lang['title_menu_finance_record']?></a>
	                                
                                        <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
                                        <a class="dropdown-item submenu_finance_revenue" href="?site=report&act=finance&subact=revenue"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_finance_revenue']?></a>
                                         <a class="dropdown-item submenu_finance_commission_staff" href="?site=report&act=finance&subact=commission_staff"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_finance_commission_staff']?></a>
                                          <a class="dropdown-item submenu_finance_commission_service" href="?site=report&act=finance&subact=commission_service"><span class="font-icon glyphicon glyphicon-usd"></span><?=$CMS->lang['title_menu_finance_commission_service']?></a>

                                        <? } ?>
                                    
                                    </div>
	                        </div>
	                        
                                <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
	                        <div class="dropdown dropdown-typical menu_inventory">
	                            <a class="dropdown-toggle" id="dd-header-form-builder" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
	                                <span class="font-icon fa fa-cubes"></span>
	                                <span class="lbl" style="display: inline-block"><?=$CMS->lang['title_menu_inventory']?></span>
	                            </a>
	
	                            <div class="dropdown-menu" aria-labelledby="dd-header-form-builder">
	                                <a class="dropdown-item submenu_inventory_quantity" href="?site=report&act=inventory&subact=quantity"><span class="font-icon glyphicon glyphicon-time"></span><?=$CMS->lang['title_menu_inventory_quantity']?></a>
	                                <a class="dropdown-item submenu_inventory_product" href="?site=report&act=inventory&subact=product"><span class="font-icon glyphicon glyphicon-cog"></span><?=$CMS->lang['title_menu_inventory_product']?></a>
                                        <a class="dropdown-item submenu_inventory_assets" href="?site=report&act=inventory&subact=assets"><span class="font-icon glyphicon glyphicon-barcode"></span><?=$CMS->lang['title_menu_inventory_assets']?></a>
                                        <a class="dropdown-item submenu_inventory_group" href="?site=report&act=inventory&subact=group"><span class="font-icon font-icon font-icon-widget"></span><?=$CMS->lang['title_menu_inventory_group']?></a>
                                        <a class="dropdown-item submenu_inventory_total" href="?site=report&act=inventory&subact=total"><span class="font-icon glyphicon glyphicon-retweet"></span><?=$CMS->lang['title_menu_inventory_total']?></a>
                                    </div>
	                        </div>
                                <? } ?>
	                        
	                    </div><!--.site-header-collapsed-in-->

	                   

	                </div><!--.site-header-collapsed-->
	            </div><!--site-header-content-in-->
	        </div><!--.site-header-content-->
	    </div><!--.container-fluid-->


	    	<div class="col-md-3 hidden-lg-up">
				 <select class="manual select2-no-search-default report_menu">
				 		<option href="?site=report&act=sales&subact=date"><?=$CMS->lang['title_menu_choice_report']?></option>
						<optgroup label="<?=$CMS->lang['title_menu_sales']?>" selected="selected" >
							<option href="?site=report&act=sales&subact=date"><?=$CMS->lang['title_menu_sales_date']?></option>
							 <? if($CMS->vars['addon_goods_enable'] == 1){ ?>

							<option href="?site=report&act=customer&subact=product"><?=$CMS->lang['title_menu_sales_store']?></option>
							 <? } ?>
							<option href="?site=report&act=sales&subact=product"><?=$CMS->lang['title_menu_sales_product']?></option>

						   <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
							<option  href="?site=report&act=sales&subact=assets"><?=$CMS->lang['title_menu_sales_assets']?></option>
						   <? } ?>
							<option href="?site=report&act=sales&subact=pgroup" ><?=$CMS->lang['title_menu_sales_pgroup']?></option>
							<option  href="?site=report&act=sales&subact=supplier"><?=$CMS->lang['title_menu_sales_supplier']?></option>

						</optgroup>
						<optgroup label="<?=$CMS->lang['title_menu_order']?>">
							<option href="?site=report&act=order&subact=date"><?=$CMS->lang['title_menu_order_date']?></option>
							<option href="?site=report&act=order&subact=price"><?=$CMS->lang['title_menu_order_price']?></option>
							<option href="?site=report&act=order&subact=product"><?=$CMS->lang['title_menu_order_product']?></option>
							  <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
							<option href="?site=report&act=order&subact=assets"><?=$CMS->lang['title_menu_order_assets']?></option>
							    <? } ?>
							<option href="?site=report&act=order&subact=status"><?=$CMS->lang['title_menu_order_status']?></option>
							<option href="?site=report&act=order&subact=user"><?=$CMS->lang['title_menu_order_user']?></option>
						 
					 
						</optgroup>
						<optgroup label="<?=$CMS->lang['title_menu_product']?>">
							<option href="?site=report&act=product&subact=bestseller" ><?=$CMS->lang['title_menu_product_bestseller']?></option>
							 <option href="?site=report&act=product&subact=price" ><?=$CMS->lang['title_menu_product_price']?></option>
							 <option href="?site=report&act=product&subact=date" ><?=$CMS->lang['title_menu_product_date']?></option>
							  <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
							 <option href="?site=report&act=product&subact=store" ><?=$CMS->lang['title_menu_product_store']?></option>
							        <? } ?>
 		 
						</optgroup>
					  <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
					    <optgroup label="<?=$CMS->lang['title_menu_assets']?>">
							<option href="?site=report&act=assets&subact=bestseller" ><?=$CMS->lang['title_menu_assets_bestseller']?></option>
							 <option href="?site=report&act=assets&subact=price" ><?=$CMS->lang['title_menu_assets_price']?></option>
							 <option href="?site=report&act=assets&subact=date" ><?=$CMS->lang['title_menu_assets_date']?></option>
						
							 <option href="?site=report&act=assets&subact=store" ><?=$CMS->lang['title_menu_assets_store']?></option>
 
						</optgroup>
				   <? } ?>
				       <optgroup label="<?=$CMS->lang['title_menu_customer']?>">
							<option href="?site=report&act=customer&subact=overview"  ><?=$CMS->lang['title_menu_customer_overview']?></option>
							<option href="?site=report&act=customer&subact=sales"  ><?=$CMS->lang['title_menu_customer_sales']?></option>
							<option href="?site=report&act=customer&subact=product"  ><?=$CMS->lang['title_menu_customer_product']?></option>
						   <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
							<option href="?site=report&act=customer&subact=assets"  ><?=$CMS->lang['title_menu_customer_assets']?></option>
							<option href="?site=report&act=customer&subact=store"  ><?=$CMS->lang['title_menu_customer_store']?></option>
						  <? } ?>  
						</optgroup>

						 <optgroup label="<?=$CMS->lang['title_menu_finance']?>">
							<option href="?site=report&act=finance&subact=daily"  ><?=$CMS->lang['title_menu_finance_daily']?></option>
							<option href="?site=report&act=finance&subact=record"  ><?=$CMS->lang['title_menu_finance_record']?></option>
							  <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
							<option href="?site=report&act=finance&subact=revenue"  ><?=$CMS->lang['title_menu_finance_revenue']?></option>

							<option href="?site=report&act=finance&subact=commission_staff"  ><?=$CMS->lang['title_menu_finance_commission_staff']?></option>
							<option href="?site=report&act=finance&subact=commission_service"  ><?=$CMS->lang['title_menu_finance_commission_service']?></option>
				 		  <? } ?> 
  
						</optgroup>

						 <? if($CMS->vars['addon_goods_enable'] == 1){ ?>
						 <optgroup label="<?=$CMS->lang['title_menu_inventory']?>">
							<option href="?site=report&act=inventory&subact=quantity" ><?=$CMS->lang['title_menu_inventory_quantity']?></option>
							<option href="?site=report&act=inventory&subact=product" ><?=$CMS->lang['title_menu_inventory_product']?></option>
							 
							 <option href="?site=report&act=inventory&subact=assets" ><?=$CMS->lang['title_menu_inventory_assets']?></option>
							 
							 <option href="?site=report&act=inventory&subact=group" ><?=$CMS->lang['title_menu_inventory_group']?></option>
							 
							 <option href="?site=report&act=inventory&subact=total" ><?=$CMS->lang['title_menu_inventory_total']?></option>
		 
						</optgroup>
						 <? } ?> 

				 </select>
					 <br/>
				 <br/>
			 </div>


 </header><!--.site-header-->
        
        <script>
            var menu = "<?=$tpl->menu_active?>";
            var submenu = "<?=$tpl->submenu_active?>";
            $('.menu_'+menu).addClass('menu_report_active');
            $('.submenu_'+menu+'_'+submenu).addClass('submenu_report_active');


            $(".report_menu").on("change",function(){
            	var link =  $(this).find("option:selected").attr("href");
            	window.location.href = link;
            });
        </script>