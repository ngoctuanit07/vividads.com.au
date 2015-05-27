$$('.adminnote-hidden').each( function(e){ e.hide(); });


function adminNoteStatus( el , id , status )
{
	var e = el.up('.adminnote-container');
	
	new Ajax.Updater(e, global_adminnote_statusurl, {
		method: 'post',
		parameters: { id: id , status: status },
	} );
	
	if (status == 1)
	{
		global_adminnote_hidden_count++;
		$('show-hidden-notes-button').show();
	}
	else
	{
		if (e.hasClassName('adminnote-hidden'))
		{
			global_adminnote_hidden_count--;
		}
	}
	
	if (global_adminnote_hidden_count < 0)
	{
		global_adminnote_hidden_count = 0;
	}
	
	$('hidden-notes-count').textContent = global_adminnote_hidden_count;
	
	return false;
}

function adminNoteEdit( el )
{
	el.up('.adminnote-container').down('.adminnote-note-content').hide();
	el.up('.adminnote-container').down('.adminnote-note-edit').show();
	adminNoteUpdateText( el.up('.adminnote-container').down('textarea') );

	return false;
}
function adminNoteEditCancel( el )
{
	el.up('.adminnote-container').down('.adminnote-note-content').show();
	el.up('.adminnote-container').down('.adminnote-note-edit').hide();

	return false;
}

function adminNoteAddCancel( el )
{
	el.up('.adminnote-container').remove();
	return false;
}

function adminNoteDelete( el , id )
{
	new Ajax.Request( global_adminnote_deleteurl, {
		method: 'post',
		parameters: { note_id: id },
	} );

	//Element.remove( el.up('.adminnote-container') );
	Effect.Fade(el.up('.adminnote-container'), { duration: 1.0 });
	
	return false;
}

function adminNoteDeleteConfirm(el, id, message)
{
	if (confirm(message))
	{
		adminNoteDelete(el, id);
	}
	
	return false;
}

function adminNotesShowAll()
{
	$$('.adminnote-hidden').each( function(e){ e.hide(); e.removeClassName('adminnote-hidden'); Effect.Appear(e, { duration: 1.0 }); });

	$('show-hidden-notes-button').hide();
	global_adminnote_hidden_count = 0;
	
	return false;
}

function adminNoteAddNewForm()
{
	new Ajax.Updater( 'notesContainer' , global_adminnote_newurl , {
		method: 'get',
		insertion: Insertion.Bottom
	} );

	return false;
}

function adminNoteUpdate(el , id)
{
	var note = el.up('span');
	
	var text = note.down('textarea').value;
	var title = note.down('select').value;//note.down('input').value;
	var type = note.down('select').value;

	new Ajax.Updater( note.up('.adminnote-container'), global_adminnote_saveurl, {
		method: 'post',
		parameters: { id: id, note: text , type: type, title: title }
	} );
}

function adminNoteAdd(el)
{
	var note = el.up('div');

	var text = note.down('textarea').value;
	var title = note.down('select').value;//note.down('input').value;
	var type = note.down('select').value;
	var path = $('adminNotePath').value;
	var path_id = $('adminNotePathId').value;

	new Ajax.Updater( note.up('.adminnote-container'), global_adminnote_saveurl, {
		method: 'post',
		parameters: { path_id: path_id , note: text , type: type, path: path, title: title },
	} );
	
}

function adminNoteUpdateText(e)
{
	// Simple Auto-Grow for textarea
	if (e.scrollHeight > e.offsetHeight)
	{  
		var h = e.scrollHeight;
		e.style.height = h+1 + "px";
	}
	  
	return true; 
}

function adminNoteTypeChange(el)
{
	var e = el.up('.notification-global');
	var classNames = $w(e.className);
	for (var i=0; i<classNames.length; ++i)
	{
		//if (classNames[i] == 'adminnote-new')
		//{
		//	e.removeClassName('adminnote-new');
		//}
		
		var str = new String(classNames[i]);
		if (str.match('adminnote-type-'))
		{
			e.removeClassName(str);
		}
	}
	e.addClassName('adminnote-type-'+ el.value);
}

function adminNoteShowMore(el)
{
	var s = el.up('.shortnote');
	var l = s.next('span');//el.up('.adminnote-note-content').down('.longnote');
	s.remove();
	//l.show();
	Effect.Appear(l, { duration: 0.5 });
}
