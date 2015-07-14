uParse('#introduce', {
	rootPath: './'
});

var torrentId = $("#torrentId").val();
$('[data-toggle=popover]').on('shown.bs.popover', function() {
		$obj = $(this);
		setTimeout(function() {
			$obj.popover('hide');
		}, 1500);
 });

//查看小伙伴
var $checkPartner = $("#partner");
$checkPartner.on("click", function(e){
	var $parent = $(this).parent();
	var $wrap = $("#seeder-leecher-list");
	if (!$(this).hasClass("showed")){
		//收起状态，要展开
		console.log('ss');
		if ($(this).attr("data-haved") == "false"){
			//还没有数据
			
			var seederCount = $parent.find(".seeder-count").text();
			var leecherCount = $parent.find(".leecher-count").text();
			if (seederCount == 0 && leecherCount == 0){
				return;
			}
			$.ajax({
				url: $("#getSeederLeecherUrl").val(),
				type: "GET",
				dataType: "json",
				data: "seederCount="+seederCount+"&leecherCount="+leecherCount+"&id="+$("#torrentId").val(),
				beforeSend: function(){$checkPartner.text("正在呼唤小伙伴们...").attr("disabled", true)},
				success: function(data){
					console.log(data);
					if (data.code == 1){
						if (data.msg.seeder){
							var seederTable = renderTable("做种者", data.msg.seeder);
							$wrap.append(seederTable);					
						}
						if(data.msg.leecher){
							var leecherTable = renderTable("下载者", data.msg.leecher);
							$wrap.append(leecherTable);
						}
						$checkPartner.attr("data-haved", "true");
						$wrap.slideDown();
						$checkPartner.text("隐藏小伙伴们").removeAttr("disabled").addClass("showed");
					}else{
						$checkPartner.text(data.msg);
						console.log(data);
					}
					if (data.updateSeederCount){
						console.log("updateSeederCount");
					}
					if (data.updateLeecherCount){
						console.log("updateLeecherCount");
					}					
				}
			})
		}else{
			//已有数据
			$wrap.slideDown();
			$checkPartner.text("隐藏小伙伴们").addClass("showed");
		}
	}else{
		$wrap.slideUp();
		$checkPartner.text("查看小伙伴们").removeClass("showed");
	}
});

function renderTable(caption, data){
	$tpl = $("#seeder-leecher-list-tpl").children();
	$tpl.find("caption").text(caption+"("+data.count+")");
	var TRHTML = [];
	$.each(data.data, function(index, item){
		TRHTML.push("<tr>");
		TRHTML.push("<td>"+item.username+"</td>");
		TRHTML.push("<td>"+(item.connectable == 1 ? "是" : "<span style=\"color: red;font-weight: bold\">否</span>")+"</td>");
		TRHTML.push("<td>"+item.uploaded_converted+"</td>");
		TRHTML.push("<td>"+item.upload_speed+"</td>");
		TRHTML.push("<td>"+item.downloaded_converted+"</td>");
		TRHTML.push("<td>"+item.download_speed+"</td>");
		if (item.downloaded == 0){
			TRHTML.push("<td>---</td>");
		}else{
			var ratio = (item.uploaded/item.downloaded).toFixed(3);
			if (ratio > 0.1){
				TRHTML.push("<td>"+ratio+"</td>");
			}else{
				TRHTML.push("<td><span style=\"color: red;font-weight: bold\">"+ratio+"</span></td>");
			}
		}			
		TRHTML.push("<td>"+(item.is_seeder == 1 ? 100 : (item.downloaded/item.torrent_size)*100).toFixed(2)+"%</td>");//完成百分比
		TRHTML.push("<td>"+(item.connect_time ? item.connect_time : 0)+"</td>");
		TRHTML.push("<td>"+item.this_report_time+"</td>");
		TRHTML.push("<td>"+item.agent+"</td>");
		TRHTML.push("</tr>");
	});
	$tpl.find("tbody").append(TRHTML.join(""));
	var toReturn = $tpl.prop("outerHTML");
	$tpl.find("tbody").empty();
	return toReturn;
}

//获取豆瓣信息
var $doubanTd = $("#douban_info");
if ($doubanTd.length) {
	var url = 'https://api.douban.com/v2/movie/subject/' + $doubanTd.attr('data-douban-id');
	$.ajax({
		url: url + '?callback=?',
		dataType: 'jsonp',
	}).done(function(result) {
// 			console.log(result);
		var HTML = createInfoHtml(result);
		$doubanTd.html(HTML);
	}).error(function(XMLHttpRequest, textStatus, errorThrown) {
		console.log(textStatus, errorThrown);
	})
}

function createInfoHtml(result) {
	const C = '◎';
	const BR = '<br/>';
	const SP = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
	var HTML = '<p class="douban-basic">' + C + '片' + SP + '名：' + result.original_title + BR;
	HTML += C + '译' + SP + '名：' + result.title + BR;
	HTML += C + '又' + SP + '名：' + arrToString(result.aka, null, '/') + BR;
	HTML += C + '年' + SP + '代：' + result.year + BR;
	HTML += C + '国家地区：' + arrToString(result.countries, null, '/') + BR;
	HTML += C + '类' + SP + '型：' + arrToString(result.genres, null, '/') + BR;
	if ('languages' in result) {
		HTML += C + '语' + SP + '言：' + arrToString(result.languages, null, '/') + BR;
	}
	if ('durations' in result) {
		HTML += C + '时' + SP + '长：' + arrToString(result.durations, null, '/') + BR;
	}
	HTML += C + '豆瓣评分：' + result.rating.average + '/10(' + result.ratings_count + ' votes)' + BR;
	HTML += C + '豆瓣链接：<a href="' + result.alt + '">' + result.alt + '</a>' + BR;
	
	HTML += C + '导' + SP + '演：' + arrToString(result.directors, 'name', '/') + BR;

	HTML += C + '演' + SP + '员：' + result.casts.shift().name + BR;
	const len = result.casts.length;
	for (var i = 1; i < len; i++) {
		HTML += SP + SP + SP + result.casts[i].name + BR;
	}
	HTML +='</p>' + BR;	
	HTML += C + '简' + SP + '介：' + BR + BR;
	HTML += '<p>' + SP + result.summary + '</p>';
	return HTML;
}

function arrToString(arr, field, d) {
	var doSub = false;
	if (typeof d === 'undefined') {
		d = '';
	} else {
		doSub = true;
	}
	var STR = '';
	const len = arr.length;
	for (var i = 0;i < len; i++) {
		if (field) {
			STR += arr[i][field] + d;
		} else {
			STR += arr[i] + d;
		}
	}
	if (doSub) {
		STR = STR.substring(0, STR.lastIndexOf(d));
	}
	return STR;
}

//查看文件列表
var $viewFileListBtn = $('#view-filelist');
var $fileListTable = $('.file-list-table');
$viewFileListBtn.click(function(e) {
	if ($viewFileListBtn.attr('data-show') == 'false') {
		$fileListTable.slideDown(function() {
			$viewFileListBtn.attr('data-show', 'true').find('.view-action').text('隐藏');
		});
	} else {
		$fileListTable.slideUp(function() {
			$viewFileListBtn.attr('data-show', 'false').find('.view-action').text('查看');
		});
	}
});

//给予奖励
$('.award').click(function(e) {
	var $btn = $(this);
	var type = $btn.attr('data-type');
	var value = $btn.attr('data-value');
	$.ajax({
		url: $('#award-url').val(),
		type: 'POST',
		dataType: 'json',
		data: 'torrentId=' + torrentId + '&type=' + type + '&value=' + value,
		timeout: 8000,
		beforeSend: function() {
			$btn.attr('disabled', 'disabled');
		}
	}).done(function(result) {
		console.log(result);
		if (result.code == 1) {
			$td = $btn.attr('data-content', '操作成功').popover('show').closest('td');
			$td.find('.award-list').append('<a href="#" class="bg-danger">' + result.data.name + '</a>');
			$td.find('.award-total').text(function(index, text) {return parseInt(text) + parseInt(result.data.value)});
			$btn.siblings().attr('disabled', 'disabled');
		} else if (result.code == 0) {
			$btn.attr('data-content', '已经操作过了').popover('show').siblings().attr('disabled', 'disabled');
		} else {
			$btn.attr('data-content', result.msg).popover('show').removeAttr('disabled');
		}
		
	}).error(function(xhr, errorText) {
		$btn.text(result.msg).removeAttr('disabled');
	})
});

/*
//添加图片表情
$("body").on("click", ".expression img", function(e){
	var $comment = $(this).parents("form").find(".comment"), sel = window.getSelection(), range = document.createRange();
	var focusNode = sel.focusNode, baseUrl = $("#baseUrl").val();
	if ($(focusNode).hasClass("comment") || $(focusNode).parents(".comment").length){
		
		range.setStart(focusNode, sel.focusOffset);
		var node = document.createElement("img");
		node.src = this.src.replace(baseUrl, "");
		range.insertNode(node);
	}
});
//添加回复和评论
var maxFloor = 1;
$("body").on("click", ".submit", function(e){
	var comment = $(this).parents("form").find(".comment").html();
	var $submit = $(this);
	var trim = $.trim(comment);
	var replace = $.trim(trim.replace(/<br>|&nbsp;|<div>|<\/div>/gi, ""));
	if (replace === ""){
		alert("请输入内容！");
		return;
	}
	var $form = $(this).parents("form");
	var floorReply = false;
	if($form.attr("data-parent")){//不是最下边直接回复框
		floorReply = true;
		if ($form.parent().hasClass("item")){
			//回复末楼
			var $oldComment = $form.parent().find(".comment-box");
			$clone = $oldComment.clone();//克隆一新的，不能在原基础上添加
			var addText = '<h6 class="floor-title">'+$form.parents(".item").find(".comment-username").text()+'&nbsp;&nbsp的评论<span class="pull-right floor-count">';
			if ($clone.children(":first").hasClass("comment-content")){
				$floorCount = $form.parent().find(".comment-box").children().children(".floor-title").children(".floor-count");
				
				addText += parseInt($floorCount.text())+1+"</span></h6>";
				$clone.children(":first").after(addText);
				
			}else{
				addText += "1</span></h6>";
				$clone.prepend(addText);
			}
			$clone.append("<h6 class=\"floor-reply\"><span class=\"reply\">回复</span></h6>");
			$clone.wrapInner("<div class=\"comment-content\"></div>");
			
			comment = $clone.html()+comment;
			
		}else if($form.parent().hasClass("comment-content")){
			//楼中楼回复
			$comment = $form.parent().clone();
			$comment.find("form").remove().end().children(".floor-reply").children(".reply").removeClass("submit-show");
			comment = $comment.prop("outerHTML") + comment;
			
		}
		
	}
	
	var $total = $("#comment-total");
	if ($total.length){
		maxFloor = parseInt($total.val())+1;
	}
	$.ajax({
		url: "comment/add",
		data: "torrentId="+torrentId+"&comment="+encodeURIComponent(comment)+"&floor="+maxFloor,
		type: "POST",
		dataType: "json",
		beforeSend: function(){$submit.text("添加中...").attr("disabled", "disabled")},
		success: function(data){
			if (data.code === 1){
				$submit.text("添加").removeAttr("disabled");
				$("#submit-form").find(".comment").empty();
				var $new = $("#tpl").children().clone();
				$new.find(".comment-floor").html(maxFloor);
			
				$new.find(".comment-username").text($("#username").val());
				$new.find(".comment-add-time").text(new Date().getTime());
				$new.find(".comment-box").html(comment);
				if ($("#comment-item").length){
					$("#comment-item").append($new);
				}else{
					$("#comment-list").append($new);
				}
				if ($total.length){
					$total.val(maxFloor);
				}else{
					maxFloor++;
				}
				if (floorReply){
					$form.remove();
				}
				var $commentTitle = $("#comment-title");
				if ($commentTitle.hasClass("no-comment")) {
					$commentTitle.text("用户评论").removeClass("no-comment");
				}
			}else{
				$submit.text(data.msg).removeAttr("disabled");
			}
		}
	})
	
});

//页面加载完成请求首页评论
$(document).ready(function(){
	var $commentTitle = $("#comment-title");
	$.ajax({
		url: "comment/list",
		type: "GET",
		dataType: "json",
		data: "torrentId="+$("#torrentId").val(),
		success: function(data){
			if (data.code === 1){
				$commentTitle.text("用户评论").removeClass("no-comment");
				$("#comment-list").html(data.msg);
			}else{
				$commentTitle.text(data.msg);
			}
		}
	})
});
//分页获取评论
var $commentList = $("#comment-list");
	$commentList.on("click", ".comment-list-nav a", function(e){
		var $click = $(this).parent();
		var total = $("#comment-page").val();
		if ($click.hasClass("disabled")){
			return;
		}
		var $page = $(this).parents(".pagination").children(".active:first");
		var page = $page.find("span:first").text();
		if ($(this).hasClass("prev")){
			page = parseInt(page)-1;
		}else if($(this).hasClass("next")){
			page = parseInt(page)+1;
		}else{
			page = parseInt($(this).children("span").text());
		} 
		 $.ajax({
				url: "comment/list",
				type: "GET",
				dataType: "json",
				data: "torrentId="+$("#torrentId").val()+"&page="+page,
				success: function(data){
					if (data.code === 1){
						$commentList.html(data.msg);
						var $nav = $commentList.find(".comment-list-nav");
						$nav.each(function(index, elem){
							$(this).find("li").removeClass("active disabled");
							$(this).find("li").eq(page).addClass("active");
							if (page == total){
								$(this).find("li").eq(page+1).attr("class", "disabled");
							}else if(page == "1"){
								$(this).find("li").eq(page-1).attr("class", "disabled");
							}
						});
						
					}
				}
		})
 });
//展示楼中楼回复框
$commentList.on("click", ".item .reply", function(e){
	
	var $this = $(this);
	if ($this.hasClass("submit-show")){
		return;
	}
	
	var $form = $("#submit-form").find("form:first").clone();
	$form.attr("data-parent", "true").find(".comment").empty();
	var $submit = $form.find(".submit");
	var $cancle = $submit.clone();
	$cancle.text("取消").on("click", function(e){
		$this.removeClass("submit-show");
		$form.remove();
	});
	$submit.after($cancle);
	$commentList.find("form").remove();
	$commentList.find(".reply").removeClass("submit-show");
	var $commentFoot = $this.parents(".comment-foot"), $footReply = $this.parents(".floor-reply");
	if ($commentFoot.length){
		$commentFoot.after($form);
	}else if($footReply.length){
		$footReply.after($form);
	}else{
		return;
	}
	$this.addClass("submit-show");
});
//鼠标移上去显示楼中楼回复按钮
$commentList.on("mouseenter mouseleave", ".comment-content", function(e){
	e.stopPropagation();
	var $reply = $(this).children(".floor-reply").find(".reply");
	if(e.type === "mouseenter"){
// 			$commentList.find(".reply").css("visibility", "hidden");
		$reply.css("visibility", "visible");
	}else{
		$reply.css("visibility", "hidden");
	}
	
});

*/

/*---------------------------------------------------------------------------*/

//新版评论

const REPLY = '<div class="reply clearfix"><textarea class="comment-reply-content" placeholder="请输入评论内容。再次点击回复取消回复框"></textarea><div class="comment-reply-action text-center"><button class="btn btn-info btn-comment-append">回复</button></div></div>';
var $commentList = $('#comment-list');

var $replyHtml = $(REPLY);
var show = false, btn = null;
$commentList.on("click", '.comment-reply', function(e) {
    e.preventDefault();
    var $btn = $(this);
    if (!show || btn !== this) {
        //没显示，或者点的不是自己
        btn = this;
        show = true;
        $btn.closest('.comment-action').append($replyHtml);
        $replyHtml.find('textarea').focus();
        $replyHtml.find('.btn-comment-append').attr('data-parentid', $btn.closest('.comment-action').attr('data-floor-id'));
    } else if (btn === this) {
        //显示了又点自己
        show = false;
        $replyHtml.detach().find('btn-comment-append').text('回复');
    }
    
});
//追加楼层
$replyHtml.on("click", '.btn-comment-append', function(e) {
   var $btn = $(this);
   var parentId = $btn.attr('data-parentid');
   var $content = $btn.parent().prev();
   var content = $content.val();
   if ($.trim(content) === '') {
		return;
	}
	if (content.length > 200) {
		alert('内容过多');
		return;
	}
   $.ajax({
		url: '/comment/add',
		type: 'POST',
		dataType: 'json',
		data: 'content=' + encodeURIComponent(content.trim()) + '&parentId=' + parentId + '&torrentId=' + torrentId,
		timeout: 8000,
		beforeSend: function() {
			$btn.attr('disabled', 'disabled').text('回复中...');
		}
	}).done(function(result) {
		$btn.removeAttr('disabled');
		if (result.code == 1) {
			$commentList.append(result.data);
			$btn.text('回复');
			$content.val('');
			show = false;
	        $replyHtml.detach();
	        var H = document.documentElement.scrollHeight;
	        var windowH = window.innerHeight;
	        if (H > windowH) {
	        	 $(document).scrollTop(H - window.innerHeight);
	        }
	       
		} else {
			$btn.text(result.msg);
			console.log(result);
		}
	}).error(function(xhr, errorText) {
		$btn.removeAttr('disabled');
		$btn.text(errorText);
	})
});

//支持与反对
var $up = $('.comment-tip:first');
var flying = false;
$commentList.on("click", '.comment-up,.comment-down', function(e) {
    e.preventDefault();
    if (flying) {
    	console.log('flying');
        return;
    }
    var $btn = $(this);
    if ($btn.hasClass('comment-up')) {
		$up.text('+ 1');
		var value=1;
		var className = '.comment-up-count';
    } else {
    	$up.text('- 1');
		var value=-1;
		var className = '.comment-down-count';
    }
    var floorId = $btn.closest('.comment-action').attr('data-floor-id');
    $.ajax({
		url: '/comment/vote',
		type: 'post',
		dataType: 'json',
		data: 'value=' + value + '&floorId=' + floorId,
		beforeSend: function() {
			flying = true;
		},
    }).done(function(result) {
		console.log(result);
		var offset = $btn.offset();
        if (result.code == 1) {
	        $btn.find(className).text(function(index, text) {return parseInt(text)+1});
	        $up.css({
	            left: offset.left,
	            top: offset.top - $up.height() - $btn.height(),
	        }).show().animate({top: "-=20px", opacity: 1}).delay(500).fadeOut(function(){
	            $up.removeAttr("style");
	            flying = false;
	        });
        }
    }).error(function(xhr, errorText) {
    	flying = false;
		console.log(errorText);
    });
    

});

//添加楼栋
var $addBuildingBtn = $('#add-building');
$addBuildingBtn.click(function(e) {
   var content = $('.comment-add textarea').val();
   if (content.trim() === '') {
		return;
	}
	if (content.length > 200) {
		alert('内容过多');
		return;
	}
	$.ajax({
		url: '/comment/add',
		type: 'POST',
		dataType: 'json',
		data: 'content=' + encodeURIComponent(content.trim()) + '&torrentId=' + torrentId,
		timeout: 8000,
		beforeSend: function() {
			$addBuildingBtn.attr('disabled', 'disabled').text('提交中...');
		}
	}).done(function(result) {
		$addBuildingBtn.removeAttr('disabled');
		if (result.code == 1) {
			$('#comment-list').append(result.data);
			$addBuildingBtn.text('提交评论');
			$('.comment-add textarea').val('');
		} else {
			$addBuildingBtn.text(result.msg);
			console.log(result);
		}
	}).error(function(xhr, errorText) {
		$addBuildingBtn.removeAttr('disabled');
		$addBuildingBtn.text(errorText);
	})
});

//获取热门评论
$(document).ready(function(){
	$.ajax({
		url: "comment/hot",
		type: "GET",
		dataType: "json",
		data: "torrentId=" + torrentId,
		success: function(result){
			if (result.code === 1){
				$('#hot-comment-head').text('热门评论');
				$commentList.append(result.data);
			}else{
				$('#hot-comment-head').text(result.msg);
			}
		}
	})
});




