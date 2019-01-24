
	function insert_thumbnail( name, url, width, height )
	{
		parent.tinyMCE.execCommand("mceInsertContent",false,'<img border="0" height="'+height+'" src="'+site_parent_domain+'/uploads/attach/thumbnail/'+url+'" width="'+width+'" />');
	}

	function insert_image( name, url, width, height )
	{
		parent.tinyMCE.execCommand("mceInsertContent",false,'<img border="0" height="'+height+'" src="'+site_parent_domain+'/uploads/attach/'+url+'" width="'+width+'" title="'+name+'" />');
	}
	
	function insert_file( name, url )
	{
		parent.tinyMCE.execCommand("mceInsertContent",false,'<a href="'+site_parent_domain+'/uploads/attach/'+url+'">'+name+'</a>');
	}
	
	function insert_url( name, url ) //
	{
		parent.tinyMCE.execCommand("mceInsertContent",false,''+site_parent_domain+'/uploads/attach/'+url);
	}
	
	function delete_attach( name, url, twidth, theight, width, height )
	{
		var newdata = parent.tinyMCE.activeEditor.getContent();
		newdata = newdata.replace('<img border="0" src="'+site_parent_domain+'/uploads/attach/thumbnail/'+url+'" />', "");
		newdata = newdata.replace('<img border="0" height="'+theight+'" src="'+site_parent_domain+'/uploads/attach/thumbnail/'+url+'" width="'+twidth+'" />', "");
		newdata = newdata.replace('<img border="0" src="'+site_parent_domain+'/uploads/attach/'+url+'" />', "");
		newdata = newdata.replace('<img border="0" height="'+height+'" src="'+site_parent_domain+'/uploads/attach/'+url+'" width="'+width+'" />', "");
		newdata = newdata.replace('<a href="'+site_parent_domain+'/uploads/attach/'+url+'">'+name+'</a>', "");
		newdata = newdata.replace(''+site_parent_domain+'/uploads/attach/'+url, "");
		parent.tinyMCE.activeEditor.setContent(newdata);
	}