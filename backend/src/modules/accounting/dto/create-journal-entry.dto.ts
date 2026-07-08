import { IsEnum, IsOptional, IsString, IsUUID, IsNumber, IsDateString } from 'class-validator';
import { JournalEntryType, Currency } from '@prisma/client';

export class CreateJournalEntryDto {
  @IsOptional()
  @IsUUID()
  tripId?: string;

  @IsOptional()
  @IsUUID()
  customerId?: string;

  @IsOptional()
  @IsUUID()
  supplierId?: string;

  @IsUUID()
  accountId: string;

  @IsEnum(JournalEntryType)
  entryType: JournalEntryType;

  @IsNumber()
  amount: number;

  @IsOptional()
  @IsEnum(Currency)
  currency?: Currency;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsDateString()
  entryDate?: string;

  @IsOptional()
  @IsString()
  referenceNo?: string;
}
