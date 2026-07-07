import { IsString, IsOptional, IsNumber, IsDateString } from 'class-validator';

export class CreateTripDto {
  @IsString()
  title: string;

  @IsString()
  destination: string;

  @IsOptional()
  @IsString()
  description?: string;

  @IsOptional()
  @IsNumber()
  price?: number;

  @IsOptional()
  @IsNumber()
  duration?: number;

  @IsOptional()
  @IsDateString()
  startDate?: string;

  @IsOptional()
  @IsDateString()
  endDate?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @IsNumber()
  maxCapacity?: number;
}
