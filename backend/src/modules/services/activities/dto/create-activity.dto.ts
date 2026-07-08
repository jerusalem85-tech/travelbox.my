import { IsString, IsOptional, IsNumber, IsDateString, IsUUID } from 'class-validator';

export class CreateActivityDto {
  @IsUUID()
  tripId: string;

  @IsString()
  name: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsString()
  location?: string;

  @IsDateString()
  date: string;

  @IsOptional()
  @IsString()
  startTime?: string;

  @IsOptional()
  @IsString()
  duration?: string;

  @IsOptional()
  @IsString()
  includes?: string;

  @IsOptional()
  @IsString()
  excludes?: string;

  @IsOptional()
  @IsString()
  bookingRef?: string;

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

export class UpdateActivityDto {
  @IsOptional() @IsString() name?: string;
  @IsOptional() @IsString() description?: string;
  @IsOptional() @IsString() location?: string;
  @IsOptional() @IsDateString() date?: string;
  @IsOptional() @IsString() startTime?: string;
  @IsOptional() @IsString() duration?: string;
  @IsOptional() @IsString() includes?: string;
  @IsOptional() @IsString() excludes?: string;
  @IsOptional() @IsString() bookingRef?: string;
  @IsOptional() @IsString() status?: string;
  @IsOptional() @IsNumber() costPrice?: number;
  @IsOptional() @IsNumber() sellPrice?: number;
  @IsOptional() @IsUUID() supplierId?: string;
  @IsOptional() @IsString() supplierRef?: string;
  @IsOptional() @IsString() notes?: string;
}
