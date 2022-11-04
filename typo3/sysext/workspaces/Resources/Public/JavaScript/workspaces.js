/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
import AjaxRequest from"@typo3/core/ajax/ajax-request.js";import{SeverityEnum}from"@typo3/backend/enum/severity.js";import $ from"jquery";import NProgress from"nprogress";import{default as Modal}from"@typo3/backend/modal.js";export default class Workspaces{constructor(){this.tid=0}renderSendToStageWindow(e){const t=e[0].result,a=$("<form />");if(void 0!==t.sendMailTo&&t.sendMailTo.length>0){a.append($("<label />",{class:"control-label"}).text(TYPO3.lang["window.sendToNextStageWindow.itemsWillBeSentTo"])),a.append($("<div />",{class:"form-group"}).append($('<button type="button" class="btn btn-default btn-xs t3js-workspace-recipients-selectall" />').text(TYPO3.lang["window.sendToNextStageWindow.selectAll"]),"&nbsp;",$('<button type="button" class="btn btn-default btn-xs t3js-workspace-recipients-deselectall" />').text(TYPO3.lang["window.sendToNextStageWindow.deselectAll"])));for(const e of t.sendMailTo)a.append($("<div />",{class:"form-check"}).append($("<input />",{type:"checkbox",name:"recipients",class:"form-check-input t3js-workspace-recipient",id:e.name,value:e.value}).prop("checked",e.checked).prop("disabled",e.disabled),$("<label />",{class:"form-check-label",for:e.name}).text(e.label)))}void 0!==t.additional&&a.append($("<div />",{class:"form-group"}).append($("<label />",{class:"control-label",for:"additional"}).text(TYPO3.lang["window.sendToNextStageWindow.additionalRecipients"]),$("<textarea />",{class:"form-control",name:"additional",id:"additional"}).text(t.additional.value),$("<span />",{class:"help-block"}).text(TYPO3.lang["window.sendToNextStageWindow.additionalRecipients.hint"]))),a.append($("<div />",{class:"form-group"}).append($("<label />",{class:"control-label",for:"comments"}).text(TYPO3.lang["window.sendToNextStageWindow.comments"]),$("<textarea />",{class:"form-control",name:"comments",id:"comments"}).text(t.comments.value)));const o=Modal.show(TYPO3.lang.actionSendToStage,a,SeverityEnum.info,[{text:TYPO3.lang.cancel,active:!0,btnClass:"btn-default",name:"cancel",trigger:()=>{o.hideModal()}},{text:TYPO3.lang.ok,btnClass:"btn-primary",name:"ok"}]);return o}sendRemoteRequest(e,t="#workspace-content-wrapper"){return NProgress.configure({parent:t,showSpinner:!1}),NProgress.start(),new AjaxRequest(TYPO3.settings.ajaxUrls.workspace_dispatch).post(e,{headers:{"Content-Type":"application/json; charset=utf-8"}}).finally((()=>NProgress.done()))}generateRemotePayload(e,t={}){return this.generateRemotePayloadBody("RemoteServer",e,t)}generateRemoteMassActionsPayload(e,t={}){return this.generateRemotePayloadBody("MassActions",e,t)}generateRemoteActionsPayload(e,t={}){return this.generateRemotePayloadBody("Actions",e,t)}generateRemotePayloadBody(e,t,a){return a instanceof Array?a.push(TYPO3.settings.Workspaces.token):a=[a,TYPO3.settings.Workspaces.token],{action:e,data:a,method:t,type:"rpc",tid:this.tid++}}}