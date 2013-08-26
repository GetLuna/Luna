/***********************************************************************

  Copyright (C) 2010-2011 Mpok
  based on code Copyright (C) 2006 Vincent Garnier
  License: http://www.gnu.org/licenses/gpl.html GPL version 2 or higher

************************************************************************/

function toolBar(textarea, bt_img_path, smilies_img_path)
{
	if (!document.createElement)
		return;

	if ((typeof(document['selection']) == 'undefined')
	&& (typeof(textarea['setSelectionRange']) == 'undefined'))
		return;

	var toolbar = document.createElement('div');
	toolbar.id = 'toolbar';
	toolbar.style.padding = '4px 0';
	var toolbar_range;
	var selected = false;
	var start;
	var before, selection, after;
	var nb_lines_bef, nb_lines_sel;
	var numbut = 0;
	var tabbut = new Array();

	var smilies = document.createElement('div');
	smilies.id = 'smilies';
	smilies.style.display = 'none';
	smilies.style.padding = '0.3em 0';

	function doEvent(obj, event, fn, mode)
	{
		if (mode)
		{
			if (obj.addEventListener)
				obj.addEventListener(event, fn, false);
			else if (obj.attachEvent)
				obj.attachEvent ('on' + event, fn);
		}
		else
		{
			if (obj.removeEventListener)
				obj.removeEventListener(event, fn, false);
			else if (obj.detachEvent)
				obj.detachEvent ('on' + event, fn);
		}
	}

	function addButton(src, title, fn)
	{
		var i = document.createElement('img');
		i.id = 'but_' + numbut;
		i.src = bt_img_path + src;
		i.title = title.replace(/&quot;/g, '"');
		i.tabIndex = 400;
		i.style.padding = '0 5px 0 0';
		toolbar.appendChild(i);
		tabbut[numbut] = fn;
		doEvent(i, 'click', fn, true);
		numbut++;
	}

	function addSmiley(src, txt)
	{
		var i = document.createElement('img');
		var htxt = txt;
		htxt = htxt.replace(new RegExp(/&amp;/g), '&');
		htxt = htxt.replace(new RegExp(/&quot;/g), '"');
		htxt = htxt.replace(new RegExp(/&lt;/g), '<');
		htxt = htxt.replace(new RegExp(/&gt;/g), '>');
		i.src = smilies_img_path + src;
		i.title = txt;
		i.tabIndex = 400;
		i.style.verticalAlign = 'middle';
		i.style.padding = '0 5px 0 0';
		doEvent(i, 'click', function() {encloseSelection(htxt, '', '')}, true);
		smilies.appendChild(i);
	}

	function addSpace(w)
	{
		var s = document.createElement('span');
		s.style.padding = '0 ' + w + 'px 0 0';
		s.appendChild(document.createTextNode(' '));
		toolbar.appendChild(s);
	}

	function unTrim(sel_range, type)
	{
		if (!type)
			var range = sel_range.duplicate();
		else
		{
			var range = document.body.createTextRange();
			range.moveToElementText(textarea);
			range.setEndPoint(type, sel_range);
		}
		var finished = false;
		var trim = range.text;
		var untrim = range.text;
		do
		{
			if (!finished)
			{
				if (range.compareEndPoints('StartToEnd', range) == 0)
					finished = true;
				else
				{
					range.moveEnd('character', -1);
					if (range.text == trim)
						untrim += "\r\n";
					else
						finished = true;
				}
			}
		}
		while (!finished);
		return(untrim);
	}

	function findSelection()
	{
		textarea.focus();
		if (typeof(textarea['setSelectionRange']) != 'undefined')
		{
			start = textarea.selectionStart;
			before = textarea.value.substring(0, start);
			var end = textarea.selectionEnd;
			after = textarea.value.substring(end);
			selection = textarea.value.substring(start, end);
		}
		else if (typeof(document['selection']) != 'undefined')
		{
			toolbar_range = document.selection.createRange();
			selection = unTrim(toolbar_range);
			before = unTrim(toolbar_range, 'EndToStart');
			after = unTrim(toolbar_range, 'StartToEnd');
			var ret_arr = before.match(/\n/g);
			if (ret_arr != null)
				nb_lines_bef = ret_arr.length;
			else
				nb_lines_bef = 0;
			ret_arr = selection.match(/\n/g);
			if (ret_arr != null)
				nb_lines_sel = ret_arr.length;
			else
				nb_lines_sel = 0;
		}
		selected = true;
	}

	function encloseSelection(prefix, suffix, fn)
	{
		if (selected == false)
			findSelection();
		selected = false;
		if (typeof(fn) == 'function')
			var res = (selection) ? fn(selection) : fn('');
		else if (fn.substring(0, 4) == 'rep=')
			var res = fn.substring(4);
		else
			var res = (selection) ? selection : '';
		var subst = prefix + res + suffix;

		var scrollPos = textarea.scrollTop;
		textarea.value = before + subst + after;
		if (typeof(textarea['setSelectionRange']) != 'undefined')
		{
			if (selection || (typeof(fn) != 'function' && fn.substring(0, 4) == 'rep='))
				textarea.setSelectionRange(start + subst.length, start + subst.length);
			else
				textarea.setSelectionRange(start + prefix.length, start + prefix.length);
		}
		else if (typeof(document['selection']) != 'undefined')
		{
			var dup = toolbar_range.duplicate();
			if (selection || (typeof(fn) != 'function' && fn.substring(0, 4) == 'rep='))
				dup.move('character', before.length - nb_lines_bef - nb_lines_sel + subst.length);
			else
				dup.move('character', before.length - nb_lines_bef + prefix.length);
			dup.select();
		}
		textarea.scrollTop = scrollPos;
	}

	function draw()
	{
		textarea.parentNode.insertBefore(smilies, textarea);
		textarea.parentNode.insertBefore(toolbar, textarea);
	}

	function singleTag(tag)
	{
		encloseSelection('[' + tag + ']', '[/' + tag + ']', '');
	}

	function btSingle(img, tag, label)
	{
		addButton(img, label, function() { singleTag(tag) });
	}

	function btPrompt_1(img, tag, label, msg_1)
	{
		addButton(img, label,
			function() {
				var var_1 = window.prompt(msg_1, '');
				if (!var_1)
					singleTag(tag);
				else
					encloseSelection('[' + tag + '=' + var_1 + ']', '[/' + tag +']', '');
			});
	}

	function btPrompt_2(img, tag, label, msg_1, msg_2, reverse)
	{
		addButton(img, label,
			function() {
				var var_1 = window.prompt(msg_1, '');
				if (!var_1)
				{
					textarea.focus();
					return;
				}
				else
				{
					findSelection();
					var var_2 = window.prompt(msg_2, selection);
					if (var_2)
					{
						if (reverse)
							encloseSelection('[' + tag + '=' + var_2 + ']', '[/' + tag +']', 'rep=' + var_1);
						else
							encloseSelection('[' + tag + '=' + var_1 + ']', '[/' + tag +']', 'rep=' + var_2);
					}
					else
						encloseSelection('[' + tag + ']', '[/' + tag + ']', 'rep=' + var_1);
				}
			});
	}

	function btPrompt_1inside(img, tag, label, msg_1)
	{
		addButton(img, label,
			function() {
				var var_1 = window.prompt(msg_1, '');
				if (!var_1)
				{
					textarea.focus();
					return;
				}
				else
					encloseSelection('[' + tag + ']', '[/' + tag + ']', 'rep=' + var_1);
			});
	}

	function switchEvent(mode)
	{
		for (var i = 0; i < toolbar.childNodes.length; i++)
		{
			var child = toolbar.childNodes[i];
			if (child.id.substring(0, 4) == 'but_')
				doEvent(child, 'click', tabbut[child.id.substring(4)], mode);
		}
	}

	function btColor(img, label)
	{
		var i = document.createElement('img');
		i.src = bt_img_path + img;
		i.title = label.replace(/&quot;/g, '"');
		i.style.padding = '0 5px 0 0';
		i.tabIndex = 400;
		toolbar.appendChild(i);
		var p = document.createElement('input');
		p.type = 'text';
		p.id = 'col_choose';
		p.size = 6;
		p.maxLength = 7;
		p.value = '#ffffff';
		p.style.height = '11px';
		p.style.marginTop = '0';
		p.style.marginRight = '2px';
		p.style.verticalAlign = '2px';
		p.style.display = 'none';
		p.tabIndex = 400;
		toolbar.insertBefore(p, i);
		var j = document.createElement('img');
		j.src = bt_img_path + img;
		j.title = label.replace(/&quot;/g, '"');
		j.style.padding = '0 5px 0 0';
		j.style.display = 'none';
		j.tabIndex = 400;
		toolbar.appendChild(j);
		var pick = new jscolor.color(i, {hash: true, caps: false, required: false, adjust: false, valueElement: p, styleElement: p, pickerOnfocus: false, pickerBorder: 2, pickerFaceColor: '#d0d0d0'});
		doEvent(i, 'click', function() {
			findSelection();
			p.style.display = 'inline';
			pick.showPicker();
			i.style.display = 'none';
			j.style.display = 'inline';
			switchEvent(false);
		}, true);
		doEvent(j, 'click', function() {
			pick.hidePicker();
			p.style.display = 'none';
			if (p.value)
				encloseSelection('[color=' + p.value + ']', '[/color]', '');
			i.style.display = 'inline';
			j.style.display = 'none';
			switchEvent(true);
			textarea.focus();
		}, true);
	}

	function btSmilies(img, label)
	{
		addButton(img, label,
			function() {
				var element = document.getElementById('smilies');
				if (element.style.display == 'block' )
				{
					textarea.focus();
					element.style.display = 'none';
				}
				else
				{
					textarea.focus();
					element.style.display = 'block';
				}
			});
	}

	function moreSmilies(txt)
	{
		var l = document.createElement('span');
		l.style.padding = '1em';
		l.style.cursor = 'pointer';
		doEvent(l, 'click', popup_smilies, true);
		l.appendChild(document.createTextNode(txt));
		smilies.appendChild(l);
	}

	function barSmilies(smilies)
	{
		for (var code in smilies)
			addSmiley(smilies[code], code);
	}

	// Methods
	this.addButton		= addButton;
	this.addSmiley		= addSmiley;
	this.addSpace		= addSpace;
	this.draw		= draw;
	this.btSingle		= btSingle;
	this.btPrompt_1		= btPrompt_1;
	this.btPrompt_1inside	= btPrompt_1inside;
	this.btPrompt_2		= btPrompt_2;
	this.btColor		= btColor;
	this.btSmilies		= btSmilies;
	this.moreSmilies	= moreSmilies;
	this.barSmilies		= barSmilies;
}
