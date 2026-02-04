import React from 'react';
import BaseNode from './BaseNode';

export default function QuestionNode({ data, selected }) {
    const preview = data.question 
        ? (data.question.length > 50 ? data.question.substring(0, 50) + '...' : data.question)
        : '(pergunta vazia)';

    const validationLabels = {
        text: 'Texto',
        number: 'Número',
        email: 'E-mail',
        phone: 'Telefone',
    };

    return (
        <BaseNode
            icon="❓"
            title={data.label || 'Pergunta'}
            preview={
                <>
                    {preview}
                    {data.variableName && (
                        <div style={{ marginTop: '4px', fontSize: '11px', color: 'var(--fb-text-muted)' }}>
                            → {`{{${data.variableName}}}`} ({validationLabels[data.validationType] || 'Texto'})
                        </div>
                    )}
                </>
            }
            nodeType="question"
            selected={selected}
        />
    );
}
