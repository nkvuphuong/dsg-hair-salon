<?= \core\ezy::render("report_menu"); ?>
<?= \core\ezy::render($tpl->action_report);?>

    <div class="container-fluid2">
      <section class="add_table main_form have_tab">
        <figure class="heading">
          <h3><span id="detail_title"><?=$tpl->detail_title;?></span>: <span class="time_title"></span></h3>
        </figure>
        <figure class="box-typical border">
          <div class="content_report">
          </div>
        </figure>
      </section>  
        <input type="hidden" name="page_name" value="<?=$tpl->page_view;?>" />
        <input type="hidden" name="page_subact" value="<?=$tpl->page_subact;?>" />
        
        
  
      <script type="text/javascript" src="/acp/javascript/acp_report.js"></script>
      <script>

          $( window ).load(function() {
            $(".change_time[btn_type='this_month']").trigger("click");
          });
        </script>  
        
        
    </div>