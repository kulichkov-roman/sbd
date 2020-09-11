<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<script>
	SibdroidBlogCommentsUpd = {

		defaultProfilePhoto: '/local/templates/sibdroid_blog/img/svg/avatar.svg',
		elementId: 0,
		ajaxUrl: '',
		
		init: function(params)
		{
			this.elementId = params.ELEMENT_ID;
			this.ajaxUrl = params.AJAX_URL;
			this.isAuthUser = window.isAuthUser;
			this.blockComments = false;
			if(this.isAuthUser){
				this.userId = parseInt(window.userId) || 0;
			}

			this.initNodes();
			this.updateCommentList();
		},

		updateCommentList: function()
		{
			var _ = this;
			
			_.sendRequest({ 
				page: 1,
				sort: $('.sort-comments__item.active').data('sort'),
				method: 'get'
			}, function(result){
				_.commentListNode.html(_.renderCommentTree(result));

				$('.blog__item__count').text(result.META.SIZE_ALL_TEXT);
				$('.blog__item_footerline__comments span').text(result.META.SIZE_ALL);
				checkRatingClass($(document));
			});
		},

		renderCommentTree: function(result)
		{
			var template = '';
			if(parseInt(result.META.SIZE_ALL_LEVEL) > 0){
				for(var i in result.ROWS){
					var row = result.ROWS[i];
					var user = result.USERS[row.UF_USER];
					var isEditable = this.isAuthUser && parseInt(row.UF_USER) === this.userId;

					//<span class="bg video-add"></span>
					var answerNode = `
						<div class="blog__item__comment_add hidden opened">
							<textarea placeholder="Написать комментарий" data-parent="${row.ID || 0}" rows="7"></textarea>
							<div class="btn btn-add-comment">Отправить</div>
							<!--<div class="blog__item__comment_add__files">
								<span class="bg img-add"></span>
							</div>-->
							<input type="file" class="hidden">
						</div>
					`;

					var editNode = isEditable ? `
						<div class="comment_edit">
							<div class="comment_settings"><span class="comment_settings__edit-btn" data-type="edit">Изменить</span></div>
							<div class="blog__item__comment_add edit_comment">
								<textarea placeholder="Написать комментарий" data-edit-comment-id="${row.ID || 0}" rows="7">${row.UF_TEXT.split('<br>').join("\n")}</textarea>
								<div class="btn btn-add-comment btn-edit-comment">Сохранить</div>
								<!--<div class="blog__item__comment_add__files">
									<span class="bg img-add"></span>
								</div>-->
								<input type="file" class="hidden">
							</div>
						</div>
					` : '';

					template += `
						<div class="comment" data-comment-id="${row.ID}">
							<div class="comment_head grid">
								<div class="grid__cell top-comment__author">
									<div class="top-comment__author_photo">
										<img src="${user.PERSONAL_PHOTO || this.defaultProfilePhoto}" alt="${user.NAME}">
									</div>
									<div class="top-comment__author_inf">
										<div class="top-comment__author_name">${user.NAME || user.LOGIN}</div>
										<div class="top-comment__author_time">${row.DATE_TEXT || 'сейчас'}</div>
									</div>
								</div>
								<div data-entity="comment" data-current="${parseInt(row.UF_LIKES_COUNT) || 0}" data-id="${row.ID}" class="js-like-check grid__cell grid__right va-middle blog__item_fright__likes ${row.RATING_CLASS || 'neitral'}">
									<span class="likes__rate likes__down">										
										<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_down"></use></svg>
									</span>
									<span class="likes__count">${parseInt(row.UF_LIKES_COUNT) || 0}</span>
									<span class="likes__rate likes__up">
										<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_arrow_up"></use></svg>
									</span>
								</div>
							</div>
							${editNode}
							<div class="comment_body">
								${row.UF_TEXT}
							</div>
							<div class="comment_footer">
								<span class="comment_footer__ans">Ответить</span>
								<div class="comment_footer__ans_node hidden">${answerNode}</div>
							</div>
							
							<div class="comment_child">
								${this.renderCommentTree(row.CHILD)}
							</div>
						</div>
					`;
				}

				/* 
				<span class="comment_footer__icon fav">
					<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_favorite"></use></svg>
				</span>
				<span class="comment_footer__icon etc">
					<svg xmlns="http://www.w3.org/2000/svg"><use xlink:href="#svg_more"></use></svg>
				</span> 
				*/

				if('NEXT_PAGE_NUM' in result.META){
					if(result.META.NEXT_PAGE_NUM > 0){
						template += `<div data-parent-id="${result.META.PARENT_COMMENT_ID}" data-page-num="${result.META.NEXT_PAGE_NUM}" class="blog__item__comment_more">Показать еще...</div>`;
					}
				}
			}

			return template;
		},

		initNodes: function()
		{
			this.commentNode = $('#comments_item_block');
			this.commentListNode = this.commentNode.find('.blog__item__comment_list');
			this.sortItemNode = this.commentNode.find('.sort-comments__item');
			this.commentAddNode = this.commentNode.find('.blog__item__comment_add');
			this.commentMoreNode = this.commentNode.find('.blog__item__comment_more');
			this.countNode = this.commentNode.find('.blog__item__count');
			this.answerNode = this.commentNode.find('.comment_footer__ans');
			this.commentAddBtn = this.commentNode.find('.btn-add-comment').not('.btn-edit-comment');
			this.commentEditBtn = this.commentNode.find('.btn-edit-comment');
			this.editBtn = this.commentNode.find('.comment_settings__edit-btn');
			this.textarea = this.commentNode.find('textarea');
		},

		initHandlers: function()
		{
			this.initNodes();

			var _ = this;
			
			this.commentAddNode.removeClass('hidden');

			this.sortItemNode.off().on('click', function(){
				if(!$(this).hasClass('active')){
					_.sortItemNode.removeClass('active');
					$(this).addClass('active');
					_.updateCommentList();
				}
			});

			this.commentMoreNode.off().on('click', function(){
				var parentId = parseInt($(this).data('parent-id')) || 0;
				var pageNum = parseInt($(this).data('page-num'));
				var sortActive = $('.sort-comments__item.active').data('sort');

				var commentList = $(this).closest('.blog__item__comment_list');
				if(parentId > 0){
					commentList = $(this).closest('.comment_child');
					sortActive = 'NEW';
				}

				_.sendRequest({
					page: pageNum,
					parentCommentId: parentId,
					sort: sortActive,
					method: 'get'
				}, function(result){
					commentList.append(_.renderCommentTree(result));
				});
				
				$(this).remove();
			});

			this.commentAddBtn.off().on('click', function(){
				var input = $(this).siblings('textarea');
				var parentId = input.data('parent');
				var parentNode = $(this).closest('.blog__item__comment_add');

				var val = input.val().trim().split("\n").filter(function(e){return e.trim().length > 0}).map(function(e){return e.trim()});
				if(val.length > 0){
					_.sendRequest({
						parentCommentId: parentId,
						text: input.val().split("\n").filter(function(e){return e.trim().length > 0}),
						method: 'add'
					}, function(result){
						if(result.ID > 0){
							parentNode.text('');
							var comment;
							var commentInsertNode = _.commentListNode;

							if(parentId > 0){
								commentInsertNode = _.commentListNode.find('[data-comment-id="' + parentId + '"] .comment_child');
							} 

							commentInsertNode.prepend(_.renderCommentTree(result));
							comment = commentInsertNode.find('.comment:first-child');
							
							comment.addClass('new');
							setTimeout(() => {
								comment.removeClass('new');
							}, 100);
							
						} else {
							parentNode.text('Ошибка добавления комментария.');
						}						
					});
				} else {
					//TO DO: Error
					alert('Введите комментарий');
				}				
			});

			this.editBtn.off().on('click', function(){
				var parent = $(this).closest('.comment_edit'),
					textarea = parent.find('textarea'),
					commentNode = parent.find('.edit_comment'),
					commentId = parseInt(textarea.data('edit-comment-id')) || 0,
					commentNodeSource = parent.siblings('.comment_body'),
					type = $(this).data('type');

				if(type === 'edit'){
					commentNode.addClass('opened').show();
					commentNodeSource.hide();
					$(this).data('type', 'cancel');
					$(this).text('Отменить');
					textarea.trigger('keyup');
				} else {
					commentNode.removeClass('opened').hide();
					commentNodeSource.show();
					$(this).data('type', 'edit');
					$(this).text('Изменить');
				}
			}); 

			this.commentEditBtn.off().on('click', function(){
				var parent = $(this).closest('.comment_edit'),
					editBtn = parent.find('.comment_settings__edit-btn'),
					commentBody = parent.siblings('.comment_body'),
					input = $(this).siblings('textarea'),
					commentId = input.data('edit-comment-id');

				var val = input.val().trim().split("\n").filter(function(e){return e.trim().length > 0}).map(function(e){return e.trim()});
				if(val.length > 0){
					_.sendRequest({
						commentId: commentId,
						text: val,
						method: 'edit'
					}, function(result){
						if(result.ID > 0 && result.TEXT !== ''){
							commentBody.html(result.TEXT);
							var valueInput = result.TEXT.split('<br>').join("\n");
							input.html(valueInput).val(valueInput);
							editBtn.trigger('click');
						} else {
							//parentNode.text('Ошибка редактирования комментария.');
							alert('Ошибка редактирования комментария.');
						}						
					});
				} else {
					//TO DO: Error
					alert('Введите изменененный комментарий');
				}		
			});

			this.commentAddNode.off().on('click', function(){
				$(this).addClass('opened');
			});

			this.commentAddNode.find('.img-add').off().on('click', function(){
				$(this).closest('.blog__item__comment_add').find('input').click();
			});

			this.answerNode.off().on('click', function(){
				$(this).siblings('.comment_footer__ans_node').toggleClass('hidden');
			});

			if(!isAuthUser){

				this.textarea.off().on('focus', function(){
					$('#auth_modal').modal();
				});
				this.textarea.prop('readonly', 'readonly');

			} else {
				this.textarea.off().on('keyup', function(){
					var countLines = $(this).val().split("\n").length;
					if(countLines > 4){
						$(this).attr('rows', countLines + 3);
					} else {
						$(this).attr('rows', "7");
					}
				});
			}

			

			initLikes(this.commentNode);
		},

		sendRequest: function(params, callback)
		{
			params.elementId = this.elementId;
			params.sessid = BX.bitrix_sessid;
			var _ = this;

			if(_.blockComments && params.method === 'add'){
				this.alertError();
				return false;
			}

			$.ajax({
				url: this.ajaxUrl,
				data: params,
				type: 'POST',

				beforeSend: function()
				{						
					_.startLoad();
				},

				success: function(data)
				{
					data = JSON.parse(data);

					var needCallback = true;
					if('TYPE' in data){
						if(data.TYPE === 'ERROR_ACTIVE_SESSION'){	
							_.blockComments = true;
							needCallback = false;
							_.alertError();	
						}
					}

					if(needCallback){
						callback(data);
					}					
						
					_.stopLoad();
					_.initHandlers();
				}
			});
		},

		alertError: function()
		{
			$('#alert_modal').find('.entity').text('комментариев');
			$('#alert_modal').modal();
		},

		startLoad: function()
		{
			this.commentNode.addClass('loading');
		},

		stopLoad: function()
		{
			this.commentNode.removeClass('loading');
		}
	};

	var arParams = <?=$arResult['JS_PARAMS']?>;
	SibdroidBlogCommentsUpd.init(arParams);

</script>

<div id="comments_item_block" class="blog__item__comment">	
	<div class="grid blog__item_head fs-20">
		<div class="grid__cell va-middle blog__item__count">
			<?//=$arResult['PHP_RESULT']['META']['SIZE_ALL_TEXT']?>
		</div>
		<div class="grid__cell grid__right">
		<div class="sort-comments">
			<div class="sort-comments__item active" data-sort="RATING">
				<span>Популярные</span>
			</div>
			<div class="sort-comments__item" data-sort="NEW">
				<span>По порядку</span>
			</div>
		</div>
		</div>
	</div>
	<div class="blog__item__comment_add hidden">
		<textarea placeholder="Написать комментарий" data-parent="0" rows="7"></textarea>
		<div class="btn btn-add-comment">Отправить</div>
		<!-- <div class="blog__item__comment_add__files">
			<span class="bg img-add"></span>
			<span class="bg video-add"></span>
		</div> -->
		<input type="file" class="hidden">
	</div>
	
	<div class="blog__item__comment_list"></div>
</div>