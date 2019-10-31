(function(){var slice=[].slice;!function($,window){"use strict";var BootstrapSwitch;return BootstrapSwitch=function(){function BootstrapSwitch(element,options){null==options&&(options={}),this.$element=$(element),this.options=$.extend({},$.fn.bootstrapSwitch.defaults,{state:this.$element.is(":checked"),size:this.$element.data("size"),animate:this.$element.data("animate"),disabled:this.$element.is(":disabled"),readonly:this.$element.is("[readonly]"),indeterminate:this.$element.data("indeterminate"),inverse:this.$element.data("inverse"),radioAllOff:this.$element.data("radio-all-off"),onColor:this.$element.data("on-color"),offColor:this.$element.data("off-color"),onText:this.$element.data("on-text"),offText:this.$element.data("off-text"),labelText:this.$element.data("label-text"),handleWidth:this.$element.data("handle-width"),labelWidth:this.$element.data("label-width"),baseClass:this.$element.data("base-class"),wrapperClass:this.$element.data("wrapper-class")},options),this.prevOptions={},this.$wrapper=$("<div>",{"class":function(_this){return function(){var classes;return classes=[""+_this.options.baseClass].concat(_this._getClasses(_this.options.wrapperClass)),classes.push(_this.options.state?_this.options.baseClass+"-on":_this.options.baseClass+"-off"),null!=_this.options.size&&classes.push(_this.options.baseClass+"-"+_this.options.size),_this.options.disabled&&classes.push(_this.options.baseClass+"-disabled"),_this.options.readonly&&classes.push(_this.options.baseClass+"-readonly"),_this.options.indeterminate&&classes.push(_this.options.baseClass+"-indeterminate"),_this.options.inverse&&classes.push(_this.options.baseClass+"-inverse"),_this.$element.attr("id")&&classes.push(_this.options.baseClass+"-id-"+_this.$element.attr("id")),classes.join(" ")}}(this)()}),this.$container=$("<div>",{"class":this.options.baseClass+"-container"}),this.$on=$("<span>",{html:this.options.onText,"class":this.options.baseClass+"-handle-on "+this.options.baseClass+"-"+this.options.onColor}),this.$off=$("<span>",{html:this.options.offText,"class":this.options.baseClass+"-handle-off "+this.options.baseClass+"-"+this.options.offColor}),this.$label=$("<span>",{html:this.options.labelText,"class":this.options.baseClass+"-label"}),this.$element.on("init.bootstrapSwitch",function(_this){return function(){return _this.options.onInit.apply(element,arguments)}}(this)),this.$element.on("switchChange.bootstrapSwitch",function(_this){return function(e){return!1===_this.options.onSwitchChange.apply(element,arguments)?_this.$element.is(":radio")?$("[name='"+_this.$element.attr("name")+"']").trigger("previousState.bootstrapSwitch",!0):_this.$element.trigger("previousState.bootstrapSwitch",!0):void 0}}(this)),this.$container=this.$element.wrap(this.$container).parent(),this.$wrapper=this.$container.wrap(this.$wrapper).parent(),this.$element.before(this.options.inverse?this.$off:this.$on).before(this.$label).before(this.options.inverse?this.$on:this.$off),this.options.indeterminate&&this.$element.prop("indeterminate",!0),this._init(),this._elementHandlers(),this._handleHandlers(),this._labelHandlers(),this._formHandler(),this._externalLabelHandler(),this.$element.trigger("init.bootstrapSwitch",this.options.state)}return BootstrapSwitch.prototype._constructor=BootstrapSwitch,BootstrapSwitch.prototype.setPrevOptions=function(){return this.prevOptions=$.extend(!0,{},this.options)},BootstrapSwitch.prototype.state=function(value,skip){return"undefined"==typeof value?this.options.state:this.options.disabled||this.options.readonly?this.$element:this.options.state&&!this.options.radioAllOff&&this.$element.is(":radio")?this.$element:(this.$element.is(":radio")?$("[name='"+this.$element.attr("name")+"']").trigger("setPreviousOptions.bootstrapSwitch"):this.$element.trigger("setPreviousOptions.bootstrapSwitch"),this.options.indeterminate&&this.indeterminate(!1),value=!!value,this.$element.prop("checked",value).trigger("change.bootstrapSwitch",skip),this.$element)},BootstrapSwitch.prototype.toggleState=function(skip){return this.options.disabled||this.options.readonly?this.$element:this.options.indeterminate?(this.indeterminate(!1),this.state(!0)):this.$element.prop("checked",!this.options.state).trigger("change.bootstrapSwitch",skip)},BootstrapSwitch.prototype.size=function(value){return"undefined"==typeof value?this.options.size:(null!=this.options.size&&this.$wrapper.removeClass(this.options.baseClass+"-"+this.options.size),value&&this.$wrapper.addClass(this.options.baseClass+"-"+value),this._width(),this._containerPosition(),this.options.size=value,this.$element)},BootstrapSwitch.prototype.animate=function(value){return"undefined"==typeof value?this.options.animate:(value=!!value,value===this.options.animate?this.$element:this.toggleAnimate())},BootstrapSwitch.prototype.toggleAnimate=function(){return this.options.animate=!this.options.animate,this.$wrapper.toggleClass(this.options.baseClass+"-animate"),this.$element},BootstrapSwitch.prototype.disabled=function(value){return"undefined"==typeof value?this.options.disabled:(value=!!value,value===this.options.disabled?this.$element:this.toggleDisabled())},BootstrapSwitch.prototype.toggleDisabled=function(){return this.options.disabled=!this.options.disabled,this.$element.prop("disabled",this.options.disabled),this.$wrapper.toggleClass(this.options.baseClass+"-disabled"),this.$element},BootstrapSwitch.prototype.readonly=function(value){return"undefined"==typeof value?this.options.readonly:(value=!!value,value===this.options.readonly?this.$element:this.toggleReadonly())},BootstrapSwitch.prototype.toggleReadonly=function(){return this.options.readonly=!this.options.readonly,this.$element.prop("readonly",this.options.readonly),this.$wrapper.toggleClass(this.options.baseClass+"-readonly"),this.$element},BootstrapSwitch.prototype.indeterminate=function(value){return"undefined"==typeof value?this.options.indeterminate:(value=!!value,value===this.options.indeterminate?this.$element:this.toggleIndeterminate())},BootstrapSwitch.prototype.toggleIndeterminate=function(){return this.options.indeterminate=!this.options.indeterminate,this.$element.prop("indeterminate",this.options.indeterminate),this.$wrapper.toggleClass(this.options.baseClass+"-indeterminate"),this._containerPosition(),this.$element},BootstrapSwitch.prototype.inverse=function(value){return"undefined"==typeof value?this.options.inverse:(value=!!value,value===this.options.inverse?this.$element:this.toggleInverse())},BootstrapSwitch.prototype.toggleInverse=function(){var $off,$on;return this.$wrapper.toggleClass(this.options.baseClass+"-inverse"),$on=this.$on.clone(!0),$off=this.$off.clone(!0),this.$on.replaceWith($off),this.$off.replaceWith($on),this.$on=$off,this.$off=$on,this.options.inverse=!this.options.inverse,this.$element},BootstrapSwitch.prototype.onColor=function(value){var color;return color=this.options.onColor,"undefined"==typeof value?color:(null!=color&&this.$on.removeClass(this.options.baseClass+"-"+color),this.$on.addClass(this.options.baseClass+"-"+value),this.options.onColor=value,this.$element)},BootstrapSwitch.prototype.offColor=function(value){var color;return color=this.options.offColor,"undefined"==typeof value?color:(null!=color&&this.$off.removeClass(this.options.baseClass+"-"+color),this.$off.addClass(this.options.baseClass+"-"+value),this.options.offColor=value,this.$element)},BootstrapSwitch.prototype.onText=function(value){return"undefined"==typeof value?this.options.onText:(this.$on.html(value),this._width(),this._containerPosition(),this.options.onText=value,this.$element)},BootstrapSwitch.prototype.offText=function(value){return"undefined"==typeof value?this.options.offText:(this.$off.html(value),this._width(),this._containerPosition(),this.options.offText=value,this.$element)},BootstrapSwitch.prototype.labelText=function(value){return"undefined"==typeof value?this.options.labelText:(this.$label.html(value),this._width(),this.options.labelText=value,this.$element)},BootstrapSwitch.prototype.handleWidth=function(value){return"undefined"==typeof value?this.options.handleWidth:(this.options.handleWidth=value,this._width(),this._containerPosition(),this.$element)},BootstrapSwitch.prototype.labelWidth=function(value){return"undefined"==typeof value?this.options.labelWidth:(this.options.labelWidth=value,this._width(),this._containerPosition(),this.$element)},BootstrapSwitch.prototype.baseClass=function(value){return this.options.baseClass},BootstrapSwitch.prototype.wrapperClass=function(value){return"undefined"==typeof value?this.options.wrapperClass:(value||(value=$.fn.bootstrapSwitch.defaults.wrapperClass),this.$wrapper.removeClass(this._getClasses(this.options.wrapperClass).join(" ")),this.$wrapper.addClass(this._getClasses(value).join(" ")),this.options.wrapperClass=value,this.$element)},BootstrapSwitch.prototype.radioAllOff=function(value){return"undefined"==typeof value?this.options.radioAllOff:(value=!!value,value===this.options.radioAllOff?this.$element:(this.options.radioAllOff=value,this.$element))},BootstrapSwitch.prototype.onInit=function(value){return"undefined"==typeof value?this.options.onInit:(value||(value=$.fn.bootstrapSwitch.defaults.onInit),this.options.onInit=value,this.$element)},BootstrapSwitch.prototype.onSwitchChange=function(value){return"undefined"==typeof value?this.options.onSwitchChange:(value||(value=$.fn.bootstrapSwitch.defaults.onSwitchChange),this.options.onSwitchChange=value,this.$element)},BootstrapSwitch.prototype.destroy=function(){var $form;return $form=this.$element.closest("form"),$form.length&&$form.off("reset.bootstrapSwitch").removeData("bootstrap-switch"),this.$container.children().not(this.$element).remove(),this.$element.unwrap().unwrap().off(".bootstrapSwitch").removeData("bootstrap-switch"),this.$element},BootstrapSwitch.prototype._width=function(){var $handles,handleWidth;return $handles=this.$on.add(this.$off),$handles.add(this.$label).css("width",""),handleWidth="auto"===this.options.handleWidth?Math.max(this.$on.width(),this.$off.width()):this.options.handleWidth,$handles.width(handleWidth),this.$label.width(function(_this){return function(index,width){return"auto"!==_this.options.labelWidth?_this.options.labelWidth:handleWidth>width?handleWidth:width}}(this)),this._handleWidth=this.$on.outerWidth(),this._labelWidth=this.$label.outerWidth(),this.$container.width(2*this._handleWidth+this._labelWidth),this.$wrapper.width(this._handleWidth+this._labelWidth)},BootstrapSwitch.prototype._containerPosition=function(state,callback){return null==state&&(state=this.options.state),this.$container.css("margin-left",function(_this){return function(){var values;return values=[0,"-"+_this._handleWidth+"px"],_this.options.indeterminate?"-"+_this._handleWidth/2+"px":state?_this.options.inverse?values[1]:values[0]:_this.options.inverse?values[0]:values[1]}}(this)),callback?setTimeout(function(){return callback()},50):void 0},BootstrapSwitch.prototype._init=function(){var init,initInterval;return init=function(_this){return function(){return _this.setPrevOptions(),_this._width(),_this._containerPosition(null,function(){return _this.options.animate?_this.$wrapper.addClass(_this.options.baseClass+"-animate"):void 0})}}(this),this.$wrapper.is(":visible")?init():initInterval=window.setInterval(function(_this){return function(){return _this.$wrapper.is(":visible")?(init(),window.clearInterval(initInterval)):void 0}}(this),50)},BootstrapSwitch.prototype._elementHandlers=function(){return this.$element.on({"setPreviousOptions.bootstrapSwitch":function(_this){return function(e){return _this.setPrevOptions()}}(this),"previousState.bootstrapSwitch":function(_this){return function(e){return _this.options=_this.prevOptions,_this.options.indeterminate&&_this.$wrapper.addClass(_this.options.baseClass+"-indeterminate"),_this.$element.prop("checked",_this.options.state).trigger("change.bootstrapSwitch",!0)}}(this),"change.bootstrapSwitch":function(_this){return function(e,skip){var state;return e.preventDefault(),e.stopImmediatePropagation(),state=_this.$element.is(":checked"),_this._containerPosition(state),state!==_this.options.state?(_this.options.state=state,_this.$wrapper.toggleClass(_this.options.baseClass+"-off").toggleClass(_this.options.baseClass+"-on"),skip?void 0:(_this.$element.is(":radio")&&$("[name='"+_this.$element.attr("name")+"']").not(_this.$element).prop("checked",!1).trigger("change.bootstrapSwitch",!0),_this.$element.trigger("switchChange.bootstrapSwitch",[state]))):void 0}}(this),"focus.bootstrapSwitch":function(_this){return function(e){return e.preventDefault(),_this.$wrapper.addClass(_this.options.baseClass+"-focused")}}(this),"blur.bootstrapSwitch":function(_this){return function(e){return e.preventDefault(),_this.$wrapper.removeClass(_this.options.baseClass+"-focused")}}(this),"keydown.bootstrapSwitch":function(_this){return function(e){if(e.which&&!_this.options.disabled&&!_this.options.readonly)switch(e.which){case 37:return e.preventDefault(),e.stopImmediatePropagation(),_this.state(!1);case 39:return e.preventDefault(),e.stopImmediatePropagation(),_this.state(!0)}}}(this)})},BootstrapSwitch.prototype._handleHandlers=function(){return this.$on.on("click.bootstrapSwitch",function(_this){return function(event){return event.preventDefault(),event.stopPropagation(),_this.state(!1),_this.$element.trigger("focus.bootstrapSwitch")}}(this)),this.$off.on("click.bootstrapSwitch",function(_this){return function(event){return event.preventDefault(),event.stopPropagation(),_this.state(!0),_this.$element.trigger("focus.bootstrapSwitch")}}(this))},BootstrapSwitch.prototype._labelHandlers=function(){return this.$label.on({click:function(e){return e.stopPropagation()},"mousedown.bootstrapSwitch touchstart.bootstrapSwitch":function(_this){return function(e){return _this._dragStart||_this.options.disabled||_this.options.readonly?void 0:(e.preventDefault(),e.stopPropagation(),_this._dragStart=(e.pageX||e.originalEvent.touches[0].pageX)-parseInt(_this.$container.css("margin-left"),10),_this.options.animate&&_this.$wrapper.removeClass(_this.options.baseClass+"-animate"),_this.$element.trigger("focus.bootstrapSwitch"))}}(this),"mousemove.bootstrapSwitch touchmove.bootstrapSwitch":function(_this){return function(e){var difference;if(null!=_this._dragStart&&(e.preventDefault(),difference=(e.pageX||e.originalEvent.touches[0].pageX)-_this._dragStart,!(difference<-_this._handleWidth||difference>0)))return _this._dragEnd=difference,_this.$container.css("margin-left",_this._dragEnd+"px")}}(this),"mouseup.bootstrapSwitch touchend.bootstrapSwitch":function(_this){return function(e){var state;if(_this._dragStart)return e.preventDefault(),_this.options.animate&&_this.$wrapper.addClass(_this.options.baseClass+"-animate"),_this._dragEnd?(state=_this._dragEnd>-(_this._handleWidth/2),_this._dragEnd=!1,_this.state(_this.options.inverse?!state:state)):_this.state(!_this.options.state),_this._dragStart=!1}}(this),"mouseleave.bootstrapSwitch":function(_this){return function(e){return _this.$label.trigger("mouseup.bootstrapSwitch")}}(this)})},BootstrapSwitch.prototype._externalLabelHandler=function(){var $externalLabel;return $externalLabel=this.$element.closest("label"),$externalLabel.on("click",function(_this){return function(event){return event.preventDefault(),event.stopImmediatePropagation(),event.target===$externalLabel[0]?_this.toggleState():void 0}}(this))},BootstrapSwitch.prototype._formHandler=function(){var $form;return $form=this.$element.closest("form"),$form.data("bootstrap-switch")?void 0:$form.on("reset.bootstrapSwitch",function(){return window.setTimeout(function(){return $form.find("input").filter(function(){return $(this).data("bootstrap-switch")}).each(function(){return $(this).bootstrapSwitch("state",this.checked)})},1)}).data("bootstrap-switch",!0)},BootstrapSwitch.prototype._getClasses=function(classes){var c,cls,i,len;if(!$.isArray(classes))return[this.options.baseClass+"-"+classes];for(cls=[],i=0,len=classes.length;len>i;i++)c=classes[i],cls.push(this.options.baseClass+"-"+c);return cls},BootstrapSwitch}(),$.fn.bootstrapSwitch=function(){var args,option,ret;return option=arguments[0],args=2<=arguments.length?slice.call(arguments,1):[],ret=this,this.each(function(){var $this,data;return $this=$(this),data=$this.data("bootstrap-switch"),data||$this.data("bootstrap-switch",data=new BootstrapSwitch(this,option)),"string"==typeof option?ret=data[option].apply(data,args):void 0}),ret},$.fn.bootstrapSwitch.Constructor=BootstrapSwitch,$.fn.bootstrapSwitch.defaults={state:!0,size:null,animate:!0,disabled:!1,readonly:!1,indeterminate:!1,inverse:!1,radioAllOff:!1,onColor:"primary",offColor:"default",onText:"ON",offText:"OFF",labelText:"&nbsp;",handleWidth:"auto",labelWidth:"auto",baseClass:"bootstrap-switch",wrapperClass:"wrapper",onInit:function(){},onSwitchChange:function(){}}}(window.jQuery,window)}).call(this);