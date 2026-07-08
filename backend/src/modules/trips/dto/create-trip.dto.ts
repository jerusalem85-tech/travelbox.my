import { IsString, IsOptional, IsEnum, IsDateString, IsBoolean } from 'class-validator';
import { TripStatus, Currency } from '@prisma/client';

export class CreateTripDto {
  @IsOptional()
  @IsString()
  name?: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsEnum(TripStatus)
  status?: TripStatus;

  @IsOptional()
  @IsDateString()
  startDate?: string;

  @IsOptional()
  @IsDateString()
  endDate?: string;

  @IsOptional()
  @IsEnum(Currency)
  currency?: Currency;

  @IsOptional()
  @IsString()
  assignedToId?: string;

  @IsOptional()
  @IsString()
  source?: string;

  @IsOptional()
  @IsString()
  internalNotes?: string;

  @IsOptional()
  tags?: string[];
}

export class UpdateTripDto {
  @IsOptional()
  @IsString()
  name?: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsEnum(TripStatus)
  status?: TripStatus;

  @IsOptional()
  @IsDateString()
  startDate?: string;

  @IsOptional()
  @IsDateString()
  endDate?: string;

  @IsOptional()
  @IsEnum(Currency)
  currency?: Currency;

  @IsOptional()
  @IsString()
  assignedToId?: string;

  @IsOptional()
  @IsString()
  source?: string;

  @IsOptional()
  @IsString()
  internalNotes?: string;

  @IsOptional()
  tags?: string[];

  @IsOptional()
  @IsBoolean()
  isActive?: boolean;
}

export class ChangeStatusDto {
  @IsEnum(TripStatus)
  status: TripStatus;
}
