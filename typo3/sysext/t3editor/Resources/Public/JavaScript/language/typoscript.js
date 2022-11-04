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
import{StreamLanguage,LanguageSupport}from"@codemirror/language";import{typoScriptStreamParser}from"@typo3/t3editor/stream-parser/typoscript.js";import TsCodeCompletion from"@typo3/t3editor/autocomplete/ts-code-completion.js";import{syntaxTree}from"@codemirror/language";export function typoscript(){const t=StreamLanguage.define(typoScriptStreamParser),e=t.data.of({autocomplete:complete});return new LanguageSupport(t,[e])}export function complete(t){t.matchBefore(/\w*/);if(!t.explicit)return null;const e=parseCodeMirror5CompatibleCompletionState(t),o=t.pos-(e.completingAfterDot?1:0),r=syntaxTree(t.state).resolveInner(o,-1),n="Document"===r.name||e.completingAfterDot?"":t.state.sliceDoc(r.from,o),s="Document"===r.name||e.completingAfterDot?t.pos:r.from;let a={start:r.from,end:o,string:n,type:r.name};/^[\w$_]*$/.test(n)||(a={start:t.pos,end:t.pos,string:"",type:"."===n?"property":null}),e.token=a;const i=TsCodeCompletion.refreshCodeCompletion(e);if(("string"===r.name||"comment"===r.name)&&tokenIsSubStringOfKeywords(n,i))return null;return{from:s,options:getCompletions(n,i).map((t=>({label:t,type:"keyword"})))}}function parseCodeMirror5CompatibleCompletionState(t){const e=t.state.sliceDoc().split(t.state.lineBreak).length,o=t.state.sliceDoc(0,t.pos).split(t.state.lineBreak).length,r=t.state.sliceDoc().split(t.state.lineBreak)[o-1],n="."===t.state.sliceDoc(t.pos-1,t.pos);return{lineTokens:extractCodemirror5StyleLineTokens(e,t),currentLineNumber:o,currentLine:r,lineCount:e,completingAfterDot:n}}function extractCodemirror5StyleLineTokens(t,e){const o=Array(t).fill("").map((()=>[]));let r=0,n=1;return syntaxTree(e.state).cursor().iterate((s=>{const a=s.type.name||s.name;if("Document"===a)return;const i=s.from,l=s.to;r<i&&e.state.sliceDoc(r,i).split(e.state.lineBreak).forEach((e=>{e&&(o[Math.min(n-1,t-1)].push({type:null,string:e,start:r,end:r+e.length}),n++,r+=e.length)}));const p=e.state.sliceDoc(s.from,s.to);n=e.state.sliceDoc(0,s.from).split(e.state.lineBreak).length,o[n-1].push({type:a,string:p,start:i,end:l}),r=l})),r<e.state.doc.length&&o[n-1].push({type:null,string:e.state.sliceDoc(r),start:r,end:e.state.doc.length}),o}function tokenIsSubStringOfKeywords(t,e){const o=t.length;for(let r=0;r<e.length;++r)if(t===e[r].substr(o))return!0;return!1}function getCompletions(t,e){const o=new Set;for(let n=0,s=e.length;n<s;++n)0!==(r=e[n]).lastIndexOf(t,0)||o.has(r)||o.add(r);var r;const n=Array.from(o);return n.sort(),n}