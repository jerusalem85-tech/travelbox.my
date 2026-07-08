import { IsEnum, IsOptional } from 'class-validator';
import { DocumentType } from '@prisma/client';

export class CreateDocumentDto {
  @IsEnum(DocumentType)
  documentType: DocumentType;
}
