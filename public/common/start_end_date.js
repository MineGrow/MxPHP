/**
 * 基于 EasyUI 的起始日期框组件
 */
;(function($) {
	/*
	该函数用于初始化组合
	_et11 为初始化元素对象
	 */
	function _et10(_et11) {
		// 获取选项对象
		var _et12 = $.data(_et11, "start_end_date");
		var opts = _et12.options;
		
		// 如果组合未被初始化
		if (_et12.combo) {
			_et12.combo.combo('destroy');
		}
		
		// 在页面中新增一个组合标记。组合对象添加到选项对象中
		_et12.combo = $("<div class='se_combo'></div>").appendTo(_et11);
		// 初始化组合
		_et12.combo.combo({
			label: opts.label,
			labelWidth: opts.labelWidth,
			width: opts.width,
			panelWidth: opts.width - opts.labelWidth,
			// 下面的内容直接在插件中确认值
			// 开发者无法修改的组合属性
			editable: false,
			reversed: true,
			hasDownArrow: false,
			validType: 'validateSE'
		});

		// 生成2个随机的 id ，用于表示面板底部和共享日历
		// 如果不设置唯一的 id，此时当实例化多个起止日期框时会出现共享日历的冲突，以及面板底部区域的冲突
		_et12.footerid   = RndNum(5);
		_et12.calendarid = RndNum(5);

		// 在组合中添加保存开始时间和结束时间的文本框
		_et60(_et11);
		// 在组合中添加共享日历
		_et20(_et11);
		// 在组合面板区域中添加内容
		_et30(_et11);
		// 初始化值
		_et80(_et11);
		// 自定义验证规则
		$.extend($.fn.validatebox.defaults.rules, {
			validateSE: {
				validator: function() {
					var start = _et12.startbox.textbox('getValue');
					var end   = _et12.endbox.textbox('getValue');
					if ((start == "" && end == "") || Date.parse(start)<Date.parse(end)) {
						return true;
					} else {
						return false;
					}
				},
				message: '请输入合法的起止日期'
			}
		});
	}

	/*
	该函数用于初始化起止日期框中的共享日历
	_et21 为初始化元素对象
	 */
	function _et20(_et21) {
		// 取出选项对象
		var _et22 = $.data(_et21, "start_end_date");
		var opts  = _et22.options;

		// 将日历添加到组合中
		_et22.calendar = $("<div class='easyui-calendar' id='"+_et22.calendarid+"'></div>").appendTo(_et22.combo);
		// 初始化日历，并将初始化后的组日历对象添加到选项对象中
		_et22.calendar.calendar({
			firstDay: "1"
		});
	}

	/*
	该函数用于初始化起止日期框中的面板区域
	_et31 为初始化元素对象
	 */
	function _et30(_et31) {
		// 取出选项对象
		var _et32 = $.data(_et31, "start_end_date");
		var opts  = _et32.options;

		// 获取组合中的面板对象
		$p = _et32.combo.combo("panel");
		// 获取面板中的主体区域对象
		$body = $p.panel("body");
		// 在主体区域中添加开始日期框
		_et40(_et31, $body);
		// 在主体区域中添加结束日期框
		_et50(_et31, $body);
		// 在面板中添加分割线
		$("<hr style='border:none; border-top:1px solid #95B8E7;'/>").appendTo($body);
		// 在面板主体区域添加工具
		_et70(_et31, $body);
		// 为面板主体区域添加一个新的风格
		$body.css('padding', '10px 5px 15px 20px');
		// 在组合中添加面板的底部按钮
		$("<div id='"+_et32.footerid+"'>"+"<div class='footer-button' style='float:right; margin-right:20px;'>"+"<a href='#' class='se-reset'>重置</a>" + "<a href='#' class='se-qd'>确定</a>" + "</div></div>").appendTo($p);
		// 设置面板的底部区域
		$p.panel({
			footer: "#" + _et32.footerid
		});
		// 获取面板的底部区域对象
		$footer = $p.panel("footer");
		// 初始化面板底部的确定按钮
		$footer.find('.se-qd').linkbutton({
			width: 42,
			height: 30,
			onClick: function() {
				var start = _et32.startbox.textbox('getValue');
				var end   = _et32.endbox.textbox('getValue');
				if (start == "" && end == "") {
					_et32.combo.combo("setText", "");
				} else {
					_et32.combo.combo("setText", _et32.startdate.datebox('getValue') + '至' + _et32.enddate.datebox('getValue'));
				}
				_et32.combo.combo('hidePanel');
				_et32.combo.combo('validate');
			}
		});

		// 初始化面板底部的重置按钮
		$footer.find('.se-reset').linkbutton({
			width: 42,
			height: 30,
			onClick: function() {
				_et80(_et31);
			}
		});
	}

	/*
	在主体区域中添加开始日期框
	_et41 为初始化元素对象
	_et42 为面板主体区域对象
	 */
	function _et40(_et41, _et42) {
		var _et43 = $.data(_et41, "start_end_date");
		var opts  = _et43.options;

		// 将开始日期框添加到面板主体区域
		_et42.append("<div>");
		_et43.startdate = $("<div><input class='se-date'></div>").appendTo(_et42);
		_et42.append("</div>");

		// 设置开始日期框属性
		_et43.startdate.datebox({
			width: 200,
			label: "开始时间",
			sharedCalendar: '#' + _et43.calendarid,
			labelWidth: 60,
			currentText: '今天',
			closeText: '关闭',
			editable: false,
			parser: function(s) {
				s = _et43.startbox.textbox('getValue');
				var t = Date.parse(s);
				if (!isNaN(t)) {
					return new Date(t);
				} else {
					return new Date();
				}
			},
			formatter: function(date) {
				var y = date.getFullYear();
				var m = date.getMonth()+1;
				var d = date.getDate();
				var t = y + '/' + m + '/' + d;
				_et43.startbox.textbox('setValue', t);
				return y + '年' + m + '月' + d + '日';
			}
		});
	}

	/*
	在主体区域中添加结束日期框
	_et51 为初始化元素对象
	_et52 为面板主体区域对象
	 */
	function _et50(_et51, _et52) {
		var _et53 = $.data(_et51, "start_end_date");
		var opts  = _et53.options;

		// 将结束日期框添加到主体面板
		_et52.append("<div style='margin-top:20px; margin-bottom:10px;'>");
		_et53.enddate = $("<input class='se-date'>").appendTo(_et52);
		_et52.append("</div>");

		// 设置结束日期框属性
		_et53.enddate.datebox({
			width: 200,
			label: '结束时间',
			sharedCalendar: "#" + _et53.calendarid,
			labelWidth: 60,
			currentText: '今天',
			closeText: '关闭',
			editable: false,
			parser: function(s) {
				s = _et53.endbox.textbox('getValue');
				var t = Date.parse(s);
				if (!isNaN(t)) {
					return new Date(t);
				} else {
					return new Date();
				}
			},
			formatter: function(date) {
				var y = date.getFullYear();
				var m = date.getMonth()+1;
				var d = date.getDate();
				var t = y + '/' + m + '/' + d;
				_et53.endbox.textbox('setValue', t);
				return y + '年' + m + '月' + d + '日';
			}
		});
	}

	// 在组合中添加保存开始日期和结束日期存储值的文本框
	function _et60(_et61) {
		var _et62 = $.data(_et61, "start_end_date");
		_et62.startbox = $("<input type='hidden'>").appendTo(_et62.combo);
		_et62.startbox.textbox();
		_et62.endbox = $("<input type='hidden'>").appendTo(_et62.combo);
		_et62.endbox.textbox();
		_et62.combo.combo("setValue", '');
	}

	/*
	在面板主体区域中添加工具栏
	_et71 为初始化元素对象
	_et72 为主板主体区域对象
	 */
	function _et70(_et71, _et72) {
		var _et73 = $.data(_et71, "start_end_date");
		var opts  = _et73.options;

		// 设置面板主体区域中的工具按钮
		_et72.append("<div style='margin-top:20px;'>");
		// 取出选项中的tools 属性
		$tools = opts.tools;
		// 遍历工具按钮，并使用选项对象值初始化为链接按钮
		$tools.forEach(function(item, index) {
			_et72.append("<a class='se-tools'></a>");
			_et72.children("a:eq(" +index+ ")").linkbutton({
				text: opts.tools[index].text,
				width: opts.tools[index].width,
				height: 30,
				group: 'tool',
				toggle: true,
				iconCls: opts.tools[index].icons,
				onClick: function() {
					console.log('_et70', opts.tools[index].handler().start, opts.tools[index].handler().end);
					_et73.startbox.textbox('setValue', opts.tools[index].handler().start);
					_et73.startdate.datebox('setValue', opts.tools[index].handler().start);
					_et73.endbox.textbox('setValue', opts.tools[index].handler().end);
					_et73.enddate.datebox('setValue', opts.tools[index].handler().end);
				}
			});
		});
		_et72.append("</div>");
	}

	/*
	重置起始日期框
	_et81 为初始化元素对象
	 */
	function _et80(_et81) {
		var _et82 = $.data(_et81, "start_end_date");
		var opts  = _et82.options;

		// 重置值
		_et82.startbox.textbox('setValue', opts.initStart);
		_et82.endbox.textbox('setValue', opts.initEnd);
		_et82.startdate.datebox('setValue', opts.initStart);
		_et82.enddate.datebox('setValue', opts.initEnd);

		if (opts.initStart == '' && opts.initEnd == '') {
			_et82.combo.combo('setText', '');
		} else {
			_et82.combo.combo('setText', _et82.startdate.datebox('getValue') + '至' + _et82.enddate.datebox('getValue'));
		}
		_et82.combo.combo('validate');
	}

	/*
	设置起始日期框
	_et91 为初始化元素对象
	 */
	function _et90(_et91, start, end) {
		var _et92 = $.data(_et91, "start_end_date");
		var opts  = _et92.options;

		// 重置值
		_et92.startbox.textbox('setValue', start);
		_et92.endbox.textbox('setValue', end);
		_et92.startdate.datebox('setValue', start);
		_et92.enddate.datebox('setValue', end);

		if (end == '' && start == '') {
			_et92.combo.combo('setText', '');
		} else {
			_et92.combo.combo('setText', _et92.startdate.datebox('getValue') + '至' + _et92.enddate.datebox('getValue'));
		}
		_et92.combo.combo('validate');
	}

	// 产生随机数
	function RndNum(n) {
		var rnd = '';
		for(var i=0; i<n; i++) {
			rnd += Math.floor(Math.random()*10);
		}
		return rnd;
	}

	/*
	实例化插件的方法
	_et1 参数可以是一个方法字符串或者配置对象
	当 _et1 为方法字符串时，_et3 为方法参数
	 */
	$.fn.start_end_date = function(_et1, _et3) {
		// 判断参数是否为字符串，如果是字符串则调用其方法
		if (typeof _et1 == 'string') {
			// _et2 中保存方法
			var _et2 = $.fn.start_end_date.methods[_et1];
			// 假如该方法存在于起止日期框方法中，则使用参数调用它
			if (_et2) {
				return _et2(this, _et3);
			} else {
				// 假如该方法不存在于起止日期方框方法中，则在组合的方法中查找
				return this.combo(_et1, _et3);
			}
		}
		// t 为调用起止日期框的元素的 jQuery 对象
		t = $(this);
		// 如果没有设置 _et1 的话，则初始化 _et1 为空对象
		_et1 = _et1 || {};

		/*
		使用 each 方法遍历所欲的调用插件元素
		该方法允许开发者同时初始化多个起止框插件
		 */
		return this.each(function() {
			// 检查元素是否已经初始化
			var _et4 = $.data(this, "start_end_date");
			if (_et4) {
				// 如果已经初始化，取出其选项对象，将选项对象与新的配置参数合并
				$.extend(_et4.options, _et1);
			} else {
				// 如果元素未被初始化，则初始化元素，并将初始化后的选项对象绑定到元素上
				$.data(this, "start_end_date", {
					// 下面的代码是将所有设置的属性全部取出后合并到一个空对象中
					options: $.extend({},
						// 空对象
						$.fn.start_end_date.defaults,	// 起止时间框默认配置
						// 取出标记中直接定义的属性、style中的属性、date-options中的属性
						// 并按照优先级合并后返回
						$.fn.start_end_date.parseOptions(this),
						// 构造参数
						_et1
					),
					previousText: ""
				});
			}
			_et10(this);
		});
	}

	$.fn.start_end_date.methods = {
		// 返回选项对象
		options: function(jq) {
			return $.data(jq[0], "start_end_date").options;
		},
		// 设置起止日期框的值
		setValues: function(jq, param) {
			var start = param.start;
			var end   = param.end;
			_et90(jq[0], start, end);
			return this;
		},
		// 获取起止日期框的值
		getValues: function(jq) {
			var t = $.data(jq[0], "start_end_date");
			return {
				start: t.startbox.textbox('getValue'),
				end: t.endbox.textbox('getValue')
			};
		},
		// 返回日历对象
		calendar: function(jq) {
			return $.data(jq[0], "start_end_date").calendar;
		},
		// 重置起止日期框的值
		reset: function(jq) {
			_et80(jq[0]);
			return this;
		},
		// 清空起止日期框的值
		clear: function(jq) {
			_et90(jq[0], '', '');
			return this;
		},
		// 调整组件的宽度
		resize: function(jq, param) {
			$.data(jq[0], "start_end_date").combo.combo("resize", param);
			return this;
		},
		// 销毁组件
		destroy: function(jq) {
			$.data(jq[0], "start_end_date").combo.combo("destroy");
		}
	}

	// 解析属性设置方式
	// _a58 参数为初始化元素
	$.fn.start_end_date.parseOptions = function(_a58) {
		var t = $(_a58);
		return $.extend({}, 
			$.fn.combo.parseOptions(_a58), 
			// 允许开发者设置 initStart、initEnd 属性
			$.parser.parseOptions(_a58, ["initStart", "initEnd"])
		);
	}

	// 设置默认配置对象 ================================================
	var date = new Date();
	var year = date.getFullYear();
	var month =  date.getMonth()+1;
	var day   = date.getDate();

	// 当前时间
	var now = year + '/' + month + '/' + day;
	var t = new Date(date.getTime() - 1000 * 60 *60 * 24 * 7);
	// 当前时间的前一周时间
	var p_week = t.getFullYear() + '/' + (t.getMonth() + 1) + '/' + t.getDate();
	var t = new Date(date.getTime() - 1000 * 60 * 60 * 24);
	// 当前时间的前一天时间
	var p_yesterday = t.getFullYear() + '/' + (t.getMonth() + 1) + '/' + t.getDate(); 
	// 当前事件到前一个月时间
	if ((month - 1) > 0) {
		var p_month = year + '/' + (month - 1) + '/' + day; 
	} else {
		var p_month = (year - 1) + '/' + (month + 11) + '/' + day;
	}

	// 当前时间的前一个季度
	if ((month - 3) > 0) {
		var p_quarter = year + '/' + (month - 3) + '/' + day;
	} else {
		var p_quarter = (year - 1) + '/' + (month + 9) + '/' + day;
	}

	// 当前时间的前一年时间
	var p_year = (year - 1) + '/' + month + '/' + day;

	// 默认配置对象
	$.fn.start_end_date.defaults = $.extend({}, 
		$.fn.combo.defaults,
		{
			tools: [
				{
					text: '过去', 
					icons: null, 
					width: 42, 
					handler: function(){
						return {
							start: p_yesterday,
							end: now
						}
					}
				},
				{
					text: '一周', 
					icons: null, 
					width: 42, 
					handler: function(){
						return {
							start: p_week,
							end: now
						}
					}
				},
				{
					text: '一月', 
					icons: null, 
					width: 42, 
					handler: function(){
						return {
							start: p_month,
							end: now
						}
					}
				},
				{
					text: '一季', 
					icons: null, 
					width: 42, 
					handler: function(){
						return {
							start: p_quarter,
							end: now
						}
					}
				},
				{
					text: '一年', 
					icons: null, 
					width: 42, 
					handler: function(){
						return {
							start: p_year,
							end: now
						}
					}
				},
			],
			initStart: '',
			initEnd: '',
			width: 250
		}
	);
})(jQuery);