if (typeof torrentId === 'undefined') {
	var torrentId = $("#torrentId").val();
}
if (typeof $commentList === 'undefined') {
	var $commentList = $('#comment-list');
}

$('[data-toggle=popover]').on('shown.bs.popover', function() {
		$obj = $(this);
		setTimeout(function() {
			$obj.popover('hide');
		}, 1500);
 });

if (typeof REPLY === 'undefined') {
	const REPLY = '<div class="reply clearfix"><textarea class="comment-reply-content" placeholder="请输入评论内容。再次点击回复取消回复框"></textarea><div class="comment-reply-action text-center"><button class="btn btn-info btn-comment-append">回复</button></div></div>';
}
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






