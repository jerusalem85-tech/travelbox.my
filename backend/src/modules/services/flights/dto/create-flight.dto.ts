import { IsString, IsOptional, IsNumber, IsDateString, IsUUID, IsInt } from 'class-validator';

export class CreateFlightDto {
  @IsUUID()
  tripId: string;

  @IsString()
  airline: string;

  @IsString()
  flightNo: string;

  @IsString()
  departureAirport: string;

  @IsString()
  arrivalAirport: string;

  @IsDateString()
  departureDate: string;

  @IsDateString()
  arrivalDate: string;

  @IsOptional()
  @IsString()
  departureTerminal?: string;

  @IsOptional()
  @IsString()
  arrivalTerminal?: string;

  @IsOptional()
  @IsString()
  bookingRef?: string;

  @IsOptional()
  @IsString()
  ticketNo?: string;

  @IsOptional()
  @IsString()
  class?: string;

  @IsOptional()
  @IsInt()
  stops?: number;

  @IsOptional()
  @IsString()
  layoverDuration?: string;

  @IsOptional()
  @IsString()
  baggageAllowance?: string;

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

export class UpdateFlightDto {
  @IsOptional()
  @IsString()
  airline?: string;

  @IsOptional()
  @IsString()
  flightNo?: string;

  @IsOptional()
  @IsString()
  departureAirport?: string;

  @IsOptional()
  @IsString()
  arrivalAirport?: string;

  @IsOptional()
  @IsDateString()
  departureDate?: string;

  @IsOptional()
  @IsDateString()
  arrivalDate?: string;

  @IsOptional()
  @IsString()
  departureTerminal?: string;

  @IsOptional()
  @IsString()
  arrivalTerminal?: string;

  @IsOptional()
  @IsString()
  bookingRef?: string;

  @IsOptional()
  @IsString()
  ticketNo?: string;

  @IsOptional()
  @IsString()
  class?: string;

  @IsOptional()
  @IsInt()
  stops?: number;

  @IsOptional()
  @IsString()
  layoverDuration?: string;

  @IsOptional()
  @IsString()
  baggageAllowance?: string;

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
