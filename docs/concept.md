# doc-writer concept

doc-writer is a tenant-aware document writing and rendering system for structured business documents.

## Core idea

document = content + document type + tenant + layout + writing style + output target

## First practical target

- tenant: Siqueira International AG
- template type: standard_letter_A4
- document type example: LOI
- input: Markdown
- preview output: HTML
- required final output: PDF
- likely PDF engine on cyon: wkhtmltopdf

## Important distinction

document type:
  what the document is
  examples: LOI, letter, memo, report, contract draft

template type:
  the technical and visual frame
  example: standard_letter_A4

An LOI normally uses a standard business letter template.
