import { IsString, IsOptional, IsNumber, IsEnum, IsDateString } from 'class-validator';
import { InvoiceStatus } from '@prisma/client';

export class CreateInvoiceDto {
  @IsOptional()
  @IsString()
  clientId?: string;

  @IsOptional()
  @IsString()
  bookingId?: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsNumber()
  amount?: number;

  @IsOptional()
  @IsEnum(InvoiceStatus)
  status?: InvoiceStatus;

  @IsOptional()
  @IsDateString()
  date?: string;
}
