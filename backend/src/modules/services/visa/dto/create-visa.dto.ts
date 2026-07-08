import { IsString, IsOptional, IsNumber, IsDateString, IsUUID } from 'class-validator';

export class CreateVisaDto {
  @IsUUID()
  tripId: string;

  @IsString()
  country: string;

  @IsString()
  visaType: string;

  @IsString()
  applicantName: string;

  @IsString()
  passportNo: string;

  @IsString()
  nationality: string;

  @IsOptional()
  @IsDateString()
  applicationDate?: string;

  @IsOptional()
  @IsDateString()
  decisionDate?: string;

  @IsOptional()
  @IsDateString()
  validFrom?: string;

  @IsOptional()
  @IsDateString()
  validUntil?: string;

  @IsOptional()
  @IsString()
  entryType?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsNumber()
  costPrice?: number;

  @IsOptional()
  @IsNumber()
  sellPrice?: number;

  @IsOptional()
  @IsUUID()
  supplierId?: string;

  @IsOptional()
  @IsString()
  supplierRef?: string;

  @IsOptional()
  @IsString()
  notes?: string;
}

export class UpdateVisaDto {
  @IsOptional() @IsString() country?: string;
  @IsOptional() @IsString() visaType?: string;
  @IsOptional() @IsString() applicantName?: string;
  @IsOptional() @IsString() passportNo?: string;
  @IsOptional() @IsString() nationality?: string;
  @IsOptional() @IsDateString() applicationDate?: string;
  @IsOptional() @IsDateString() decisionDate?: string;
  @IsOptional() @IsDateString() validFrom?: string;
  @IsOptional() @IsDateString() validUntil?: string;
  @IsOptional() @IsString() entryType?: string;
  @IsOptional() @IsString() status?: string;
  @IsOptional() @IsNumber() costPrice?: number;
  @IsOptional() @IsNumber() sellPrice?: number;
  @IsOptional() @IsUUID() supplierId?: string;
  @IsOptional() @IsString() supplierRef?: string;
  @IsOptional() @IsString() notes?: string;
}
