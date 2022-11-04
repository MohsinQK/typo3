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
import AjaxRequest from"@typo3/core/ajax/ajax-request.js";import DocumentService from"@typo3/core/document-service.js";import DebounceEvent from"@typo3/core/event/debounce-event.js";import RegularEvent from"@typo3/core/event/regular-event.js";var Selectors,ProposalModes;!function(e){e.toggleButton=".t3js-form-field-slug-toggle",e.recreateButton=".t3js-form-field-slug-recreate",e.inputField=".t3js-form-field-slug-input",e.readOnlyField=".t3js-form-field-slug-readonly",e.hiddenField=".t3js-form-field-slug-hidden"}(Selectors||(Selectors={})),function(e){e.AUTO="auto",e.RECREATE="recreate",e.MANUAL="manual"}(ProposalModes||(ProposalModes={}));class SlugElement{constructor(e,t){this.options=null,this.fullElement=null,this.manuallyChanged=!1,this.readOnlyField=null,this.inputField=null,this.hiddenField=null,this.request=null,this.fieldsToListenOn={},this.options=t,this.fieldsToListenOn=this.options.listenerFieldNames||{},DocumentService.ready().then((t=>{this.fullElement=t.querySelector(e),this.inputField=this.fullElement.querySelector(Selectors.inputField),this.readOnlyField=this.fullElement.querySelector(Selectors.readOnlyField),this.hiddenField=this.fullElement.querySelector(Selectors.hiddenField),this.registerEvents()}))}registerEvents(){const e=Object.values(this.getAvailableFieldsForProposalGeneration()).map((e=>`[data-formengine-input-name="${e}"]`)),t=this.fullElement.querySelector(Selectors.recreateButton);e.length>0&&"new"===this.options.command&&new DebounceEvent("input",(()=>{this.manuallyChanged||this.sendSlugProposal(ProposalModes.AUTO)})).delegateTo(document,e.join(",")),e.length>0||this.hasPostModifiersDefined()?new RegularEvent("click",(e=>{e.preventDefault(),this.readOnlyField.classList.contains("hidden")&&(this.readOnlyField.classList.toggle("hidden",!1),this.inputField.classList.toggle("hidden",!0)),this.sendSlugProposal(ProposalModes.RECREATE)})).bindTo(t):(t.classList.add("disabled"),t.disabled=!0),new DebounceEvent("input",(()=>{this.manuallyChanged=!0,this.sendSlugProposal(ProposalModes.MANUAL)})).bindTo(this.inputField);const s=this.fullElement.querySelector(Selectors.toggleButton);new RegularEvent("click",(e=>{e.preventDefault();const t=this.readOnlyField.classList.contains("hidden");this.readOnlyField.classList.toggle("hidden",!t),this.inputField.classList.toggle("hidden",t),t?(this.inputField.value!==this.readOnlyField.value?this.readOnlyField.value=this.inputField.value:(this.manuallyChanged=!1,this.fullElement.querySelector(".t3js-form-proposal-accepted").classList.add("hidden"),this.fullElement.querySelector(".t3js-form-proposal-different").classList.add("hidden")),this.hiddenField.value=this.readOnlyField.value):this.hiddenField.value=this.inputField.value})).bindTo(s)}sendSlugProposal(e){const t={};if(e===ProposalModes.AUTO||e===ProposalModes.RECREATE){for(const[e,s]of Object.entries(this.getAvailableFieldsForProposalGeneration()))t[e]=document.querySelector('[data-formengine-input-name="'+s+'"]').value;!0===this.options.includeUidInValues&&(t.uid=this.options.recordId.toString())}else t.manual=this.inputField.value;this.request instanceof AjaxRequest&&this.request.abort(),this.request=new AjaxRequest(TYPO3.settings.ajaxUrls.record_slug_suggest),this.request.post({values:t,mode:e,tableName:this.options.tableName,pageId:this.options.pageId,parentPageId:this.options.parentPageId,recordId:this.options.recordId,language:this.options.language,fieldName:this.options.fieldName,command:this.options.command,signature:this.options.signature}).then((async t=>{const s=await t.resolve(),l="/"+s.proposal.replace(/^\//,""),i=this.fullElement.querySelector(".t3js-form-proposal-accepted"),o=this.fullElement.querySelector(".t3js-form-proposal-different");i.classList.toggle("hidden",s.hasConflicts),o.classList.toggle("hidden",!s.hasConflicts),(s.hasConflicts?o:i).querySelector("span").innerText=l;this.hiddenField.value!==s.proposal&&this.fullElement.querySelector("input[data-formengine-input-name]").dispatchEvent(new Event("change",{bubbles:!0,cancelable:!0})),e===ProposalModes.AUTO||e===ProposalModes.RECREATE?(this.readOnlyField.value=s.proposal,this.hiddenField.value=s.proposal,this.inputField.value=s.proposal):this.hiddenField.value=s.proposal})).finally((()=>{this.request=null}))}getAvailableFieldsForProposalGeneration(){const e={};for(const[t,s]of Object.entries(this.fieldsToListenOn)){null!==document.querySelector('[data-formengine-input-name="'+s+'"]')&&(e[t]=s)}return e}hasPostModifiersDefined(){return Array.isArray(this.options.config.generatorOptions.postModifiers)&&this.options.config.generatorOptions.postModifiers.length>0}}export default SlugElement;