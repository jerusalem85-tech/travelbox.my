import { IsString, IsOptional, IsNumber, IsEnum, IsDateString } from 'class-validator';
import { BookingStatus } from '@prisma/client';

export class CreateBookingDto {
  @IsString()
  clientId: string;

  @IsOptional()
  @IsString()
  tripId?: string;

  @IsOptional()
  @IsEnum(BookingStatus)
  status?: BookingStatus;

  @IsOptional()
  @IsNumber()
  totalAmount?: number;

  @IsOptional()
  @IsNumber()
  paidAmount?: number;

  @IsOptional()
  @IsString()
  notes?: string;

  @IsOptional()
  @IsDateString()
  travelDate?: string;
}
