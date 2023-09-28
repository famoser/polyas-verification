<script setup lang="ts">
defineProps<{
  content: string
}>()

type PolyasDocument = {
  nodes: any[]
}

type PolyasBlock = {
  nodes: any[]
  type: string
}

type PolyasText = {
  text: string
}

function isDocument(document: any): document is PolyasDocument {
  return 'object' in document && document['object'] === 'document'
}

function isBlock(block: any): block is PolyasBlock {
  return 'object' in block && block['object'] === 'block'
}

function isText(text: any): text is PolyasText {
  return 'object' in text && text['object'] === 'text'
}

const renderDocument = (document: any) => {
  return isDocument(document) ? document.nodes.map((node) => renderBlock(node)).join('\n') : ''
}

const renderBlock = (content: any) => {
  return isBlock(content) && content.type === 'paragraph' ? content.nodes.map((content) => renderParagraph(content)).join('\n') : ''
}

const renderParagraph = (text: any) => {
  return isText(text) ? text.text : ''
}
</script>

<template>
  <p>
    {{ renderDocument(content) }}
  </p>
</template>
