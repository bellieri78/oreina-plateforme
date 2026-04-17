{{-- Block Editor Component --}}
<div x-data="blockEditor(@js($submission->content_blocks ?? []))" x-cloak class="block-editor">
    {{-- Hidden input for form submission --}}
    <input type="hidden" name="content_blocks" :value="JSON.stringify(blocks)">

    {{-- Block list --}}
    <div class="blocks-container">
        <template x-for="(block, index) in blocks" :key="block.id">
            <div class="block-item" :class="'block-type-' + block.type" :data-index="index">
                {{-- Block header --}}
                <div class="block-header">
                    <div class="block-type-label">
                        <span x-text="getBlockTypeLabel(block.type)"></span>
                    </div>
                    <div class="block-actions">
                        <button type="button" @click="moveBlock(index, -1)" :disabled="index === 0" class="block-btn" title="Monter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                            </svg>
                        </button>
                        <button type="button" @click="moveBlock(index, 1)" :disabled="index === blocks.length - 1" class="block-btn" title="Descendre">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <button type="button" @click="duplicateBlock(index)" class="block-btn" title="Dupliquer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5a3.375 3.375 0 00-3.375-3.375H9.75" />
                            </svg>
                        </button>
                        <button type="button" @click="removeBlock(index)" class="block-btn block-btn-danger" title="Supprimer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Block content based on type --}}
                <div class="block-content">
                    {{-- Heading block --}}
                    <template x-if="block.type === 'heading'">
                        <div class="heading-block">
                            <select x-model="block.level" class="heading-level">
                                <option value="2">Titre H2</option>
                                <option value="3">Sous-titre H3</option>
                            </select>
                            <input type="text" x-model="block.content" placeholder="Titre de la section..." class="heading-input">
                        </div>
                    </template>

                    {{-- Paragraph block --}}
                    <template x-if="block.type === 'paragraph'">
                        <div class="paragraph-block">
                            <div class="text-toolbar">
                                <div class="toolbar-group">
                                    <button type="button" @click="formatText(index, 'bold')" class="toolbar-btn" title="Gras (Ctrl+B)">
                                        <strong>B</strong>
                                    </button>
                                    <button type="button" @click="formatText(index, 'italic')" class="toolbar-btn" title="Italique (Ctrl+I)">
                                        <em>I</em>
                                    </button>
                                    <button type="button" @click="formatText(index, 'underline')" class="toolbar-btn" title="Souligné">
                                        <span style="text-decoration: underline;">U</span>
                                    </button>
                                </div>
                                <div class="toolbar-separator"></div>
                                <div class="toolbar-group">
                                    <button type="button" @click="formatText(index, 'sub')" class="toolbar-btn" title="Indice">
                                        X<sub>2</sub>
                                    </button>
                                    <button type="button" @click="formatText(index, 'sup')" class="toolbar-btn" title="Exposant">
                                        X<sup>2</sup>
                                    </button>
                                </div>
                                <div class="toolbar-separator"></div>
                                <div class="toolbar-group">
                                    <button type="button" @click="insertSpecialChar(index, 'alpha')" class="toolbar-btn toolbar-btn-sm" title="Alpha (α)">α</button>
                                    <button type="button" @click="insertSpecialChar(index, 'beta')" class="toolbar-btn toolbar-btn-sm" title="Beta (β)">β</button>
                                    <button type="button" @click="insertSpecialChar(index, 'micro')" class="toolbar-btn toolbar-btn-sm" title="Micro (µ)">µ</button>
                                    <button type="button" @click="insertSpecialChar(index, 'plusminus')" class="toolbar-btn toolbar-btn-sm" title="Plus/Moins (±)">±</button>
                                    <button type="button" @click="insertSpecialChar(index, 'degree')" class="toolbar-btn toolbar-btn-sm" title="Degré (°)">°</button>
                                </div>
                            </div>
                            <textarea x-model="block.content" placeholder="Saisissez votre paragraphe..." class="paragraph-input" rows="4" :id="'para-' + block.id" @keydown.ctrl.b.prevent="formatText(index, 'bold')" @keydown.ctrl.i.prevent="formatText(index, 'italic')"></textarea>
                            <div class="format-hint">
                                <small>Formatage : &lt;strong&gt;gras&lt;/strong&gt;, &lt;em&gt;italique&lt;/em&gt;, &lt;sub&gt;indice&lt;/sub&gt;, &lt;sup&gt;exposant&lt;/sup&gt;</small>
                            </div>
                        </div>
                    </template>

                    {{-- Image block --}}
                    <template x-if="block.type === 'image'">
                        <div class="image-block">
                            {{-- Image alignment options --}}
                            <div class="image-toolbar" x-show="block.url">
                                <span class="toolbar-label">Alignement :</span>
                                <div class="alignment-buttons">
                                    <button type="button" @click="block.align = 'left'" :class="{ 'active': block.align === 'left' }" class="align-btn" title="Gauche">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h10.5m-10.5 5.25h16.5" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="block.align = 'center'" :class="{ 'active': block.align === 'center' || !block.align }" class="align-btn" title="Centre">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M6.75 12h10.5M3.75 17.25h16.5" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="block.align = 'right'" :class="{ 'active': block.align === 'right' }" class="align-btn" title="Droite">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M9.75 12h10.5M3.75 17.25h16.5" />
                                        </svg>
                                    </button>
                                    <button type="button" @click="block.align = 'full'" :class="{ 'active': block.align === 'full' }" class="align-btn" title="Pleine largeur">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5M3.75 17.25h16.5" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="image-size-control">
                                    <label>Largeur :</label>
                                    <select x-model="block.width" class="width-select">
                                        <option value="auto">Auto</option>
                                        <option value="25">25%</option>
                                        <option value="50">50%</option>
                                        <option value="75">75%</option>
                                        <option value="100">100%</option>
                                    </select>
                                </div>
                            </div>
                            <div class="image-preview" x-show="block.url" :class="'align-' + (block.align || 'center')">
                                <img :src="block.url" alt="Preview" :style="block.width && block.width !== 'auto' ? 'width:' + block.width + '%' : ''">
                            </div>
                            <div class="image-upload" x-show="!block.url">
                                <input type="file" @change="handleImageUpload($event, index)" accept="image/*" :id="'img-upload-' + block.id">
                                <label :for="'img-upload-' + block.id" class="upload-label">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="32" height="32">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                    </svg>
                                    <span>Cliquez pour ajouter une image</span>
                                </label>
                            </div>
                            <div class="image-url-input" x-show="!block.url">
                                <span>ou</span>
                                <input type="text" x-model="block.url" placeholder="URL de l'image..." class="url-input">
                            </div>
                            <button type="button" x-show="block.url" @click="block.url = ''" class="remove-image-btn">Supprimer l'image</button>
                            <input type="text" x-model="block.caption" placeholder="Legende (ex: Fig. 1 - Description...)" class="caption-input">
                        </div>
                    </template>

                    {{-- Table block --}}
                    <template x-if="block.type === 'table'">
                        <div class="table-block">
                            <div class="table-controls">
                                <button type="button" @click="addTableColumn(index)" class="table-ctrl-btn">+ Colonne</button>
                                <button type="button" @click="addTableRow(index)" class="table-ctrl-btn">+ Ligne</button>
                                <button type="button" @click="removeTableColumn(index)" class="table-ctrl-btn" :disabled="block.headers.length <= 1">- Colonne</button>
                                <button type="button" @click="removeTableRow(index)" class="table-ctrl-btn" :disabled="block.rows.length <= 1">- Ligne</button>
                            </div>
                            <div class="table-editor">
                                <table>
                                    <thead>
                                        <tr>
                                            <template x-for="(header, hIdx) in block.headers" :key="'h-'+hIdx">
                                                <th>
                                                    <input type="text" x-model="block.headers[hIdx]" placeholder="En-tete">
                                                </th>
                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(row, rIdx) in block.rows" :key="'r-'+rIdx">
                                            <tr>
                                                <template x-for="(cell, cIdx) in row" :key="'c-'+cIdx">
                                                    <td>
                                                        <input type="text" x-model="block.rows[rIdx][cIdx]" placeholder="Cellule">
                                                    </td>
                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                            <input type="text" x-model="block.caption" placeholder="Legende du tableau (ex: Tab. 1 - ...)" class="caption-input">
                        </div>
                    </template>

                    {{-- List block --}}
                    <template x-if="block.type === 'list'">
                        <div class="list-block">
                            <div class="list-type-select">
                                <label>
                                    <input type="radio" x-model="block.listType" value="unordered"> A puces
                                </label>
                                <label>
                                    <input type="radio" x-model="block.listType" value="ordered"> Numerotee
                                </label>
                            </div>
                            <div class="list-items">
                                <template x-for="(item, iIdx) in block.items" :key="'li-'+iIdx">
                                    <div class="list-item-row">
                                        <span class="list-marker" x-text="block.listType === 'ordered' ? (iIdx + 1) + '.' : '•'"></span>
                                        <input type="text" x-model="block.items[iIdx]" placeholder="Element de liste..." class="list-item-input">
                                        <button type="button" @click="removeListItem(index, iIdx)" class="remove-item-btn" :disabled="block.items.length <= 1">×</button>
                                    </div>
                                </template>
                            </div>
                            <button type="button" @click="addListItem(index)" class="add-item-btn">+ Ajouter un element</button>
                        </div>
                    </template>

                    {{-- Quote block --}}
                    <template x-if="block.type === 'quote'">
                        <div class="quote-block">
                            <textarea x-model="block.content" placeholder="Texte de la citation..." class="quote-input" rows="3"></textarea>
                            <input type="text" x-model="block.source" placeholder="Source (optionnel)" class="quote-source-input">
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Empty state --}}
        <div x-show="blocks.length === 0" class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="48" height="48">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            <p>Aucun contenu. Ajoutez des blocs ci-dessous.</p>
        </div>
    </div>

    {{-- Add block buttons --}}
    <div class="add-block-bar">
        <span class="add-block-label">Ajouter :</span>
        <button type="button" @click="addBlock('heading')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            Titre
        </button>
        <button type="button" @click="addBlock('paragraph')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
            </svg>
            Paragraphe
        </button>
        <button type="button" @click="addBlock('image')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            Image
        </button>
        <button type="button" @click="addBlock('table')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
            </svg>
            Tableau
        </button>
        <button type="button" @click="addBlock('list')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            Liste
        </button>
        <button type="button" @click="addBlock('quote')" class="add-block-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
            Citation
        </button>
        <span style="border-left: 1px solid #d1d5db; height: 24px; margin: 0 8px;"></span>
        <button type="button" @click="$refs.mdFileInput.click()" class="add-block-btn" style="background:#6366f1;color:white;" :disabled="importing" :style="importing && 'opacity:0.6;cursor:wait'">
            <template x-if="!importing">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
            </template>
            <span x-text="importing ? 'Conversion en cours...' : 'Importer un document'"></span>
        </button>
        <input type="file" x-ref="mdFileInput" accept=".md,.txt,.markdown,.docx" style="display:none"
               @change="importMarkdown($event)">
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

.block-editor {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 0.75rem;
    overflow: hidden;
}

.blocks-container {
    min-height: 200px;
    padding: 1rem;
}

.block-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
    overflow: hidden;
    transition: box-shadow 0.2s;
}

.block-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.block-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0.75rem;
    background: #f3f4f6;
    border-bottom: 1px solid #e5e7eb;
}

.block-type-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: 0.05em;
}

.block-type-heading .block-type-label { color: #0d9488; }
.block-type-paragraph .block-type-label { color: #3b82f6; }
.block-type-image .block-type-label { color: #8b5cf6; }
.block-type-table .block-type-label { color: #f59e0b; }
.block-type-list .block-type-label { color: #10b981; }
.block-type-quote .block-type-label { color: #ec4899; }

.block-actions {
    display: flex;
    gap: 0.25rem;
}

.block-btn {
    padding: 0.25rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.15s;
}

.block-btn:hover:not(:disabled) {
    background: #f3f4f6;
    color: #374151;
}

.block-btn:disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.block-btn-danger:hover:not(:disabled) {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.block-content {
    padding: 0.75rem;
}

/* Heading block */
.heading-block {
    display: flex;
    gap: 0.5rem;
}

.heading-level {
    width: 120px;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.heading-input {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 1.1rem;
    font-weight: 600;
}

/* Paragraph block */
.text-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.25rem;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e5e7eb;
}

.toolbar-group {
    display: flex;
    gap: 0.125rem;
}

.toolbar-separator {
    width: 1px;
    height: 20px;
    background: #e5e7eb;
    margin: 0 0.25rem;
}

.toolbar-btn {
    padding: 0.25rem 0.5rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 0.8rem;
    min-width: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toolbar-btn:hover {
    background: #f3f4f6;
}

.toolbar-btn-sm {
    padding: 0.125rem 0.375rem;
    font-size: 0.9rem;
}

.format-hint {
    margin-top: 0.375rem;
    color: #9ca3af;
    font-size: 0.7rem;
}

.paragraph-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.9rem;
    line-height: 1.6;
    resize: vertical;
    min-height: 100px;
}

/* Image block */
.image-block {
    text-align: center;
}

.image-toolbar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem;
    background: #f9fafb;
    border-radius: 0.375rem;
    margin-bottom: 0.75rem;
}

.toolbar-label {
    font-size: 0.75rem;
    color: #6b7280;
    font-weight: 500;
}

.alignment-buttons {
    display: flex;
    gap: 0.25rem;
}

.align-btn {
    padding: 0.375rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    cursor: pointer;
    color: #6b7280;
    transition: all 0.15s;
}

.align-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.align-btn.active {
    background: #0d9488;
    border-color: #0d9488;
    color: white;
}

.image-size-control {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.width-select {
    padding: 0.25rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.image-preview {
    margin-bottom: 0.75rem;
}

.image-preview.align-left {
    text-align: left;
}

.image-preview.align-center {
    text-align: center;
}

.image-preview.align-right {
    text-align: right;
}

.image-preview.align-full img {
    width: 100%;
    max-height: none;
}

.image-preview img {
    max-width: 100%;
    max-height: 300px;
    border-radius: 0.375rem;
    border: 1px solid #e5e7eb;
}

.image-upload input[type="file"] {
    display: none;
}

.upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2rem;
    border: 2px dashed #d1d5db;
    border-radius: 0.5rem;
    cursor: pointer;
    color: #9ca3af;
    transition: all 0.2s;
}

.upload-label:hover {
    border-color: #0d9488;
    color: #0d9488;
}

.image-url-input {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.75rem;
    font-size: 0.8rem;
    color: #9ca3af;
}

.url-input {
    flex: 1;
    padding: 0.375rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.8rem;
}

.remove-image-btn {
    margin: 0.5rem 0;
    padding: 0.25rem 0.5rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.25rem;
    color: #dc2626;
    font-size: 0.75rem;
    cursor: pointer;
}

.caption-input {
    width: 100%;
    margin-top: 0.5rem;
    padding: 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.85rem;
    font-style: italic;
}

/* Table block */
.table-controls {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.table-ctrl-btn {
    padding: 0.25rem 0.5rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    cursor: pointer;
}

.table-ctrl-btn:hover:not(:disabled) {
    background: #f3f4f6;
}

.table-ctrl-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.table-editor {
    overflow-x: auto;
}

.table-editor table {
    width: 100%;
    border-collapse: collapse;
}

.table-editor th,
.table-editor td {
    border: 1px solid #d1d5db;
    padding: 0;
}

.table-editor th input,
.table-editor td input {
    width: 100%;
    padding: 0.5rem;
    border: none;
    font-size: 0.85rem;
}

.table-editor th input {
    background: #f9fafb;
    font-weight: 600;
}

/* List block */
.list-type-select {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
    font-size: 0.85rem;
}

.list-type-select label {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
}

.list-item-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.375rem;
}

.list-marker {
    width: 20px;
    text-align: center;
    color: #6b7280;
    font-weight: 500;
}

.list-item-input {
    flex: 1;
    padding: 0.375rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.85rem;
}

.remove-item-btn {
    padding: 0.125rem 0.375rem;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 0.25rem;
    color: #dc2626;
    cursor: pointer;
    font-size: 0.9rem;
}

.remove-item-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.add-item-btn {
    padding: 0.375rem 0.75rem;
    background: #f0fdf4;
    border: 1px solid #86efac;
    border-radius: 0.25rem;
    color: #15803d;
    font-size: 0.8rem;
    cursor: pointer;
    margin-top: 0.5rem;
}

/* Quote block */
.quote-block {
    border-left: 4px solid #e5e7eb;
    padding-left: 1rem;
}

.quote-input {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.9rem;
    font-style: italic;
    resize: vertical;
}

.quote-source-input {
    width: 100%;
    margin-top: 0.5rem;
    padding: 0.375rem 0.5rem;
    border: 1px solid #d1d5db;
    border-radius: 0.25rem;
    font-size: 0.8rem;
    color: #6b7280;
}

/* Empty state */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 3rem;
    color: #9ca3af;
    text-align: center;
}

.empty-state svg {
    margin-bottom: 1rem;
}

/* Add block bar */
.add-block-bar {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.add-block-label {
    font-size: 0.8rem;
    font-weight: 500;
    color: #6b7280;
}

.add-block-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    font-size: 0.8rem;
    font-weight: 500;
    color: #374151;
    cursor: pointer;
    transition: all 0.15s;
}

.add-block-btn:hover {
    background: #0d9488;
    border-color: #0d9488;
    color: white;
}

.add-block-btn svg {
    opacity: 0.7;
}
</style>

<script>
// Ensure Alpine.js is loaded and register the block editor component
(function() {
    function registerBlockEditor() {
        if (typeof Alpine === 'undefined') {
            // Wait for Alpine to be available
            document.addEventListener('alpine:init', registerBlockEditor);
            return;
        }

        Alpine.data('blockEditor', (initialBlocks = []) => ({
        blocks: Array.isArray(initialBlocks) && initialBlocks.length > 0
            ? initialBlocks.map((b, i) => ({ ...b, id: b.id || 'block-' + Date.now() + '-' + i }))
            : [],
        blockIdCounter: Date.now(),

        hasUnsavedChanges: false,

        init() {
            console.log('Block editor initialized with', this.blocks.length, 'blocks');
            this.$watch('blocks', () => {
                this.hasUnsavedChanges = true;
                this.$dispatch('blocks-changed', { count: this.blocks.length });
            });
        },

        generateId() {
            return 'block-' + (this.blockIdCounter++);
        },

        getBlockTypeLabel(type) {
            const labels = {
                heading: 'Titre',
                paragraph: 'Paragraphe',
                image: 'Image',
                table: 'Tableau',
                list: 'Liste',
                quote: 'Citation'
            };
            return labels[type] || type;
        },

        addBlock(type) {
            const block = { id: this.generateId(), type: type };

            switch (type) {
                case 'heading':
                    block.level = '2';
                    block.content = '';
                    break;
                case 'paragraph':
                    block.content = '';
                    break;
                case 'image':
                    block.url = '';
                    block.caption = '';
                    block.align = 'center';
                    block.width = 'auto';
                    break;
                case 'table':
                    block.headers = ['Colonne 1', 'Colonne 2'];
                    block.rows = [['', '']];
                    block.caption = '';
                    break;
                case 'list':
                    block.listType = 'unordered';
                    block.items = [''];
                    break;
                case 'quote':
                    block.content = '';
                    block.source = '';
                    break;
            }

            this.blocks = [...this.blocks, block];
            console.log('Added block:', type, 'Total:', this.blocks.length);
        },

        removeBlock(index) {
            if (confirm('Supprimer ce bloc ?')) {
                this.blocks = this.blocks.filter((_, i) => i !== index);
            }
        },

        moveBlock(index, direction) {
            const newIndex = index + direction;
            if (newIndex >= 0 && newIndex < this.blocks.length) {
                const newBlocks = [...this.blocks];
                const [removed] = newBlocks.splice(index, 1);
                newBlocks.splice(newIndex, 0, removed);
                this.blocks = newBlocks;
                console.log('Moved block from', index, 'to', newIndex);
            }
        },

        duplicateBlock(index) {
            const block = JSON.parse(JSON.stringify(this.blocks[index]));
            block.id = this.generateId();
            const newBlocks = [...this.blocks];
            newBlocks.splice(index + 1, 0, block);
            this.blocks = newBlocks;
        },

        importing: false,

        async importMarkdown(event) {
            const file = event.target.files[0];
            if (!file) return;

            if (this.blocks.length > 0) {
                if (!confirm('Cela va remplacer les ' + this.blocks.length + ' blocs existants et les champs sidebar. Continuer ?')) {
                    event.target.value = '';
                    return;
                }
            }

            const ext = file.name.split('.').pop().toLowerCase();
            const isWord = ['docx'].includes(ext);

            this.importing = true;

            try {
                let markdown;

                if (isWord) {
                    // Client-side conversion via mammoth.js
                    if (typeof mammoth === 'undefined') {
                        alert('La bibliothèque de conversion Word n\'est pas chargée. Réessayez.');
                        this.importing = false;
                        event.target.value = '';
                        return;
                    }
                    const arrayBuffer = await file.arrayBuffer();
                    const result = await mammoth.convertToMarkdown({arrayBuffer: arrayBuffer});
                    markdown = result.value;
                } else {
                    // .md/.txt: read file content directly
                    markdown = await file.text();
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
                    || document.querySelector('input[name="_token"]')?.value
                    || '{{ csrf_token() }}';

                const response = await fetch('{{ route("admin.submissions.import-markdown", $submission->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ markdown_content: markdown }),
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'Erreur lors de l\'import.');
                    return;
                }

                this.blocks = data.blocks;
                this.blockIdCounter = data.blocks.length + 1;

                // Pre-fill sidebar fields
                if (data.references !== undefined) {
                    const refsEl = document.getElementById('sidebar-references');
                    if (refsEl) refsEl.value = data.references;

                    const affilEl = document.getElementById('sidebar-affiliations');
                    if (affilEl) affilEl.value = data.authors_affiliations;

                    const ackEl = document.getElementById('sidebar-acknowledgements');
                    if (ackEl) ackEl.value = data.acknowledgements;

                    const displayAuthorsEl = document.getElementById('sidebar-display-authors');
                    if (displayAuthorsEl && data.display_authors) displayAuthorsEl.value = data.display_authors;

                    const titleEnEl = document.getElementById('sidebar-title-en');
                    if (titleEnEl && data.title_en) titleEnEl.value = data.title_en;

                    const abstractEl = document.getElementById('sidebar-display-abstract');
                    if (abstractEl && data.display_abstract) {
                        abstractEl.innerHTML = data.display_abstract;
                        document.getElementById('input-display-abstract').value = data.display_abstract;
                    }

                    const summaryEl = document.getElementById('sidebar-display-summary');
                    if (summaryEl && data.display_summary) {
                        summaryEl.innerHTML = data.display_summary;
                        document.getElementById('input-display-summary').value = data.display_summary;
                    }
                }

                // Show detected title banner if different from current title
                if (data.detected_title) {
                    const currentTitle = @js($submission->title);
                    const detected = data.detected_title.trim();
                    if (detected && detected !== currentTitle) {
                        const banner = document.getElementById('detected-title-banner');
                        const titleText = document.getElementById('detected-title-text');
                        const updateBtn = document.getElementById('update-title-btn');
                        if (banner && titleText) {
                            titleText.textContent = detected;
                            banner.style.display = 'flex';

                            if (updateBtn) {
                                updateBtn.onclick = async () => {
                                    const resp = await fetch('{{ route("admin.submissions.update-title", $submission->id) }}', {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken,
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({ _method: 'PATCH', title: detected }),
                                    });
                                    if (resp.ok) {
                                        banner.style.display = 'none';
                                        document.querySelector('.layout-title').textContent = detected;
                                    }
                                };
                            }
                        }
                    }
                }

                const extra = data.references !== undefined
                    ? ' + références, affiliations et remerciements pré-remplis'
                    : '';
                alert('Import réussi : ' + data.count + ' blocs créés' + extra + '.');
            } catch (error) {
                alert('Erreur : ' + error.message);
            }

            this.importing = false;
            event.target.value = '';
        },

        formatText(index, format) {
            const textarea = document.getElementById('para-' + this.blocks[index].id);
            if (!textarea) return;

            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            const selectedText = text.substring(start, end);

            if (selectedText) {
                let wrapped = '';
                switch (format) {
                    case 'bold':
                        wrapped = '<strong>' + selectedText + '</strong>';
                        break;
                    case 'italic':
                        wrapped = '<em>' + selectedText + '</em>';
                        break;
                    case 'underline':
                        wrapped = '<u>' + selectedText + '</u>';
                        break;
                    case 'sub':
                        wrapped = '<sub>' + selectedText + '</sub>';
                        break;
                    case 'sup':
                        wrapped = '<sup>' + selectedText + '</sup>';
                        break;
                }
                const newBlocks = [...this.blocks];
                newBlocks[index] = { ...newBlocks[index], content: text.substring(0, start) + wrapped + text.substring(end) };
                this.blocks = newBlocks;

                // Restore cursor position
                this.$nextTick(() => {
                    textarea.focus();
                    textarea.setSelectionRange(start + wrapped.length, start + wrapped.length);
                });
            }
        },

        insertSpecialChar(index, charType) {
            const textarea = document.getElementById('para-' + this.blocks[index].id);
            if (!textarea) return;

            const chars = {
                'alpha': 'α',
                'beta': 'β',
                'gamma': 'γ',
                'delta': 'δ',
                'micro': 'µ',
                'plusminus': '±',
                'degree': '°',
                'times': '×',
                'divide': '÷'
            };

            const char = chars[charType] || '';
            if (!char) return;

            const start = textarea.selectionStart;
            const text = textarea.value;
            const newText = text.substring(0, start) + char + text.substring(start);

            const newBlocks = [...this.blocks];
            newBlocks[index] = { ...newBlocks[index], content: newText };
            this.blocks = newBlocks;

            // Restore cursor position after the inserted character
            this.$nextTick(() => {
                textarea.focus();
                textarea.setSelectionRange(start + 1, start + 1);
            });
        },

        handleImageUpload(event, index) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const newBlocks = [...this.blocks];
                    newBlocks[index] = { ...newBlocks[index], url: e.target.result };
                    this.blocks = newBlocks;
                };
                reader.readAsDataURL(file);
            }
        },

        addTableColumn(index) {
            const newBlocks = [...this.blocks];
            const block = { ...newBlocks[index] };
            block.headers = [...block.headers, 'Nouvelle colonne'];
            block.rows = block.rows.map(row => [...row, '']);
            newBlocks[index] = block;
            this.blocks = newBlocks;
        },

        removeTableColumn(index) {
            const block = this.blocks[index];
            if (block.headers.length > 1) {
                const newBlocks = [...this.blocks];
                const newBlock = { ...newBlocks[index] };
                newBlock.headers = newBlock.headers.slice(0, -1);
                newBlock.rows = newBlock.rows.map(row => row.slice(0, -1));
                newBlocks[index] = newBlock;
                this.blocks = newBlocks;
            }
        },

        addTableRow(index) {
            const newBlocks = [...this.blocks];
            const block = { ...newBlocks[index] };
            const newRow = new Array(block.headers.length).fill('');
            block.rows = [...block.rows, newRow];
            newBlocks[index] = block;
            this.blocks = newBlocks;
        },

        removeTableRow(index) {
            const block = this.blocks[index];
            if (block.rows.length > 1) {
                const newBlocks = [...this.blocks];
                const newBlock = { ...newBlocks[index] };
                newBlock.rows = newBlock.rows.slice(0, -1);
                newBlocks[index] = newBlock;
                this.blocks = newBlocks;
            }
        },

        addListItem(index) {
            const newBlocks = [...this.blocks];
            const block = { ...newBlocks[index] };
            block.items = [...block.items, ''];
            newBlocks[index] = block;
            this.blocks = newBlocks;
        },

        removeListItem(blockIndex, itemIndex) {
            const block = this.blocks[blockIndex];
            if (block.items.length > 1) {
                const newBlocks = [...this.blocks];
                const newBlock = { ...newBlocks[blockIndex] };
                newBlock.items = newBlock.items.filter((_, i) => i !== itemIndex);
                newBlocks[blockIndex] = newBlock;
                this.blocks = newBlocks;
            }
        }
    }));
    }

    // Try to register immediately if Alpine is already loaded
    if (typeof Alpine !== 'undefined') {
        registerBlockEditor();
    } else {
        // Otherwise wait for alpine:init event
        document.addEventListener('alpine:init', registerBlockEditor);
    }
})();
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.8.0/mammoth.browser.min.js"></script>
