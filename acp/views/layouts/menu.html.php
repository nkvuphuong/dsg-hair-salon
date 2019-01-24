<rootmenu>
    <menu>
        <name><?=$CMS->lang['menu_main'];?></name>
        <key>main</key>
        <url><?=$CMS->vars['root_domain'];?></url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_store'];?></name>
        <key>store</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=store</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_promotion'];?></name>
        <key>promotion</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=promotion</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_campaign'];?></name>
        <key>campaign</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=campaign</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_collection'];?></name>
        <key>collection</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=collection</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_warehouse'];?></name>
        <key>warehouse</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=warehouse</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_order'];?></name>
        <key>order</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=order</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_orderer'];?></name>
        <key>orderer</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=orderer</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_print'];?></name>
        <key>printing</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=printing</url>
    </menu>



    <menu>
        <name><?=$CMS->lang['menu_news'];?></name>
        <key>news</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=news</url>
        <submenu><name><?=$CMS->lang['menu_news_list'];?></name><key>news</key><url><?=$CMS->vars['root_domain'];?>/?site=news</url></submenu>
        <submenu><name><?=$CMS->lang['menu_news_add'];?></name><key>news</key><url><?=$CMS->vars['root_domain'];?>/?site=news&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_parent_cat_news'];?></name><key>config_parent_news</key><url><?=$CMS->vars['root_domain'];?>/?site=config_parent_news</url></submenu>
        <submenu><name><?=$CMS->lang['menu_newstpl'];?></name><key>newstpl</key><url><?=$CMS->vars['root_domain'];?>/?site=newstpl</url></submenu>

        <submenu><name><?=$CMS->lang['menu_tags'];?></name><key>tags</key><url><?=$CMS->vars['root_domain'];?>/?site=tags</url></submenu>

    </menu>

    <menu>
        <name><?=$CMS->lang['menu_album'];?></name>
        <key>config_album</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=album</url>

        <submenu><name><?=$CMS->lang['menu_album_list'];?></name><key>album</key><url><?=$CMS->vars['root_domain'];?>/?site=album</url></submenu>
        <submenu><name><?=$CMS->lang['menu_album_add'];?></name><key>album</key><url><?=$CMS->vars['root_domain'];?>/?site=album&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_config_album'];?></name><key>config_album</key><url><?=$CMS->vars['root_domain'];?>/?site=config_album</url></submenu>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_video'];?></name>
        <key>config_video</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=video</url>
        <submenu><name><?=$CMS->lang['menu_video_list'];?></name><key>video</key><url><?=$CMS->vars['root_domain'];?>/?site=video</url></submenu>
        <submenu><name><?=$CMS->lang['menu_video_add'];?></name><key>video</key><url><?=$CMS->vars['root_domain'];?>/?site=video&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_config_video'];?></name><key>config_video</key><url><?=$CMS->vars['root_domain'];?>/?site=config_video</url></submenu>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_consultants'];?></name>
        <key>consultants</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=consultants</url>
        <submenu><name><?=$CMS->lang['menu_consultants_list'];?></name><key>consultants</key><url><?=$CMS->vars['root_domain'];?>/?site=consultants</url></submenu>
        <submenu><name><?=$CMS->lang['menu_consultants_add'];?></name><key>consultants</key><url><?=$CMS->vars['root_domain'];?>/?site=consultants&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_cat_consultants'];?></name><key>cat_consultants</key><url><?=$CMS->vars['root_domain'];?>/?site=cat_consultants</url></submenu>
        <submenu><name><?=$CMS->lang['menu_cat_consultants_add'];?></name><key>cat_consultants</key><url><?=$CMS->vars['root_domain'];?>/?site=cat_consultants&amp;act=add</url></submenu>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_customer'];?></name>
        <key>customer</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=customer</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_group_customer'];?></name>
        <key>group_customer</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=group_customer</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_accounts'];?></name>
        <key>accounts</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=accounts</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_accounts_type'];?></name>
        <key>accounts_type</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=accounts_type</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_customer'];?></name>
        <key>customer</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=customer</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_transactions'];?></name>
        <key>transactions</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=transactions</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_manufacture'];?></name>
        <key>manufacture</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=manufacture</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_category'];?></name>
        <key>product_group</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=product_group</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_product'];?></name>
        <key>product</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=product</url>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_assets'];?></name>
        <key>assets</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=assets</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_config_price'];?></name>
        <key>config_price</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=config_price</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_member'];?></name>
        <key>member</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=member</url>
        <submenu><name><?=$CMS->lang['menu_member_list'];?></name><key>member</key><url><?=$CMS->vars['root_domain'];?>/?site=member</url></submenu>
        <submenu><name><?=$CMS->lang['menu_member_add'];?></name><key>member</key><url><?=$CMS->vars['root_domain'];?>/?site=member&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_business'];?></name><key>business</key><url><?=$CMS->vars['root_domain'];?>/?site=business</url></submenu>

    </menu>
    <menu>
        <name><?=$CMS->lang['menu_management'];?></name>
        <key>management</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=management</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_training'];?></name>
        <key>course</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=course</url>
        <submenu><name><?=$CMS->lang['menu_course'];?></name><key>course</key><url><?=$CMS->vars['root_domain'];?>/?site=course</url></submenu>
        <submenu><name><?=$CMS->lang['menu_mcourse'];?></name><key>mcourse</key><url><?=$CMS->vars['root_domain'];?>/?site=mcourse</url></submenu>
        <submenu><name><?=$CMS->lang['menu_libdocument'];?></name><key>libdocument</key><url><?=$CMS->vars['root_domain'];?>/?site=libdocument</url></submenu>

    </menu>
    <menu>
        <name><?=$CMS->lang['menu_donors'];?></name>
        <key>donors</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=donors</url>
    </menu>
    <menu>
        <name><?=$CMS->lang['menu_function'];?></name>
        <key>function</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=poll</url>
        <submenu><name><?=$CMS->lang['menu_poll'];?></name><key>poll</key><url><?=$CMS->vars['root_domain'];?>/?site=poll</url></submenu>
        <submenu><name><?=$CMS->lang['menu_box'];?></name><key>box</key><url><?=$CMS->vars['root_domain'];?>/?site=box</url></submenu>
        <submenu><name><?=$CMS->lang['menu_comment'];?></name><key>comment</key><url><?=$CMS->vars['root_domain'];?>/?site=comment</url></submenu>

    </menu>
    <menu>
        <name><?=$CMS->lang['menu_email'];?></name>
        <key>email</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=email</url>
        <submenu><name><?=$CMS->lang['menu_email_list'];?></name><key>email</key><url><?=$CMS->vars['root_domain'];?>/?site=email</url></submenu>
        <submenu><name><?=$CMS->lang['menu_email_add'];?></name><key>email_add</key><url><?=$CMS->vars['root_domain'];?>/?site=email&amp;act=add</url></submenu>
        <!--            <submenu><name><?=$CMS->lang['menu_emailmass_list'];?></name><key>emailmass</key><url><?=$CMS->vars['root_domain'];?>/?site=emailmass</url></submenu>
                    <submenu><name><?=$CMS->lang['menu_emailmass_add'];?></name><key>emailmass_add</key><url><?=$CMS->vars['root_domain'];?>/?site=emailmass&amp;act=add</url></submenu>-->
        <submenu><name><?=$CMS->lang['menu_emailtpl_list'];?></name><key>emailtpl</key><url><?=$CMS->vars['root_domain'];?>/?site=emailtpl</url></submenu>
        <submenu><name><?=$CMS->lang['menu_emailtpl_add'];?></name><key>emailtpl_add</key><url><?=$CMS->vars['root_domain'];?>/?site=emailtpl&amp;act=add</url></submenu>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_interface'];?></name>
        <key>pages</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=logo</url>
        <submenu><name><?=$CMS->lang['menu_announce'];?></name><key>announce</key><url><?=$CMS->vars['root_domain'];?>/?site=announce</url></submenu>
        <submenu><name><?=$CMS->lang['menu_pages_list'];?></name><key>pages</key><url><?=$CMS->vars['root_domain'];?>/?site=pages</url></submenu>
        <!-- <submenu><name><?=$CMS->lang['menu_html_list'];?></name><key>html</key><url><?=$CMS->vars['root_domain'];?>/?site=html</url></submenu>
        
       <submenu><name><?=$CMS->lang['menu_config_news'];?></name><key>config_news</key><url><?=$CMS->vars['root_domain'];?>/?site=config_news</url></submenu>-->
        <submenu><name><?=$CMS->lang['menu_link'];?></name><key>link</key><url><?=$CMS->vars['root_domain'];?>/?site=link</url></submenu>
        <submenu><name><?=$CMS->lang['menu_logo_list'];?></name><key>logo</key><url><?=$CMS->vars['root_domain'];?>/?site=logo</url></submenu>
        <submenu><name><?=$CMS->lang['menu_logo_position'];?></name><key>logo_position</key><url><?=$CMS->vars['root_domain'];?>/?site=logo_position</url></submenu>

    </menu>

    <menu>
        <name><?=$CMS->lang['menu_user'];?></name>
        <key>user</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=user</url>
        <submenu><name><?=$CMS->lang['menu_user_add'];?></name><key>user_add</key><url><?=$CMS->vars['root_domain'];?>/?site=user&amp;act=add</url></submenu>
        <submenu><name><?=$CMS->lang['menu_user_list'];?></name><key>user</key><url><?=$CMS->vars['root_domain'];?>/?site=user</url></submenu>
        <submenu><name><?=$CMS->lang['menu_group'];?></name><key>group</key><url><?=$CMS->vars['root_domain'];?>/?site=group</url></submenu>
    </menu>

    <menu>
        <name><?=$CMS->lang['menu_report'];?></name>
        <key>report</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=report&amp;act=report_all</url>


    </menu>
    <menu>
        <name><?=$CMS->lang['menu_system'];?></name>
        <key>config</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=config</url>
        <submenu><name><?=$CMS->lang['menu_config_list'];?></name><key>config</key><url><?=$CMS->vars['root_domain'];?>/?site=config</url></submenu>
        <submenu><name><?=$CMS->lang['menu_logs'];?></name><key>logs</key><url><?=$CMS->vars['root_domain'];?>/?site=logs</url></submenu>
        <!-- <submenu><name><?=$CMS->lang['menu_cache'];?></name><key>cache</key><url><?=$CMS->vars['root_domain'];?>/?site=cache</url></submenu> -->
        <submenu><name><?=$CMS->lang['menu_phpinfo'];?></name><key>phpinfo</key><url><?=$CMS->vars['root_domain'];?>/?site=phpinfo</url></submenu>
        <submenu><name><?=$CMS->lang['menu_optimize'];?></name><key>optimize</key><url><?=$CMS->vars['root_domain'];?>/?site=optimize</url></submenu>
    </menu>
    <menu>
        <name>Shopify</name>
        <key>shopify</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=shopify</url>
    </menu>
	
	<menu>
        <name><?=$CMS->lang['menu_fanpage'];?></name>
        <key>fanpage</key>
        <url><?=$CMS->vars['root_domain'];?>/?site=facebook&act=fanpage</url>
    </menu>
</rootmenu>