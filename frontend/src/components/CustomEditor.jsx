"use client";

import { CKEditor } from "@ckeditor/ckeditor5-react";
import DecoupledEditor from "@ckeditor/ckeditor5-build-decoupled-document";
import { useState } from "react";

export default function CustomEditor() {
  const [content, setContent] = useState(`
    <p>This Privacy Policy is meant to help you understand what data we collect, why we collect it.</p>
    <p>User can only access app after login.</p>
    <p>Happy Life Vastu app is a subscription based app.</p>
  `);

  return (
    <div className="bg-[#111118] border border-white/10 p-6 rounded-2xl shadow-xl">

      <CKEditor
        editor={DecoupledEditor}
        data={content}
        config={{
          toolbar: [
            "undo",
            "redo",
            "|",
            "bold",
            "italic",
            "underline",
            "strikethrough",
            "|",
            "fontSize",
            "fontFamily",
            "fontColor",
            "fontBackgroundColor",
            "|",
            "alignment",
            "|",
            "bulletedList",
            "numberedList",
            "outdent",
            "indent",
            "|",
            "blockQuote",
            "link",
            "insertTable",
            "mediaEmbed",
            "|",
            "specialCharacters",
            "|",
            "horizontalLine",
            "removeFormat",
            "|",
            "sourceEditing"
          ],
        }}
        onReady={(editor) => {
          const toolbarContainer =
            editor.ui.view.toolbar.element;
          editor.ui.getEditableElement().parentElement.insertBefore(
            toolbarContainer,
            editor.ui.getEditableElement()
          );
        }}
        onChange={(event, editor) => {
          setContent(editor.getData());
        }}
      />

      <style jsx global>{`
        .ck-editor__editable {
          background-color: #1a1a24 !important;
          color: #ffffff !important;
          min-height: 350px;
        }

        .ck-toolbar {
          background-color: #14141c !important;
          border: 1px solid rgba(255,255,255,0.1) !important;
        }

        .ck-button {
          color: #cccccc !important;
        }

        .ck-button.ck-on {
          background: linear-gradient(90deg, #a855f7, #22d3ee) !important;
          color: white !important;
        }

        .ck-dropdown__panel {
          background-color: #1a1a24 !important;
          color: white !important;
        }
      `}</style>

    </div>
  );
}
